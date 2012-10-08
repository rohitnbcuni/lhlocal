package com.nbcu.ots.lighthouse.comments.email;

import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.util.ArrayList;
import java.util.List;
import java.util.Properties;
import java.util.regex.Pattern;

import javax.mail.Address;
import javax.mail.Flags;
import javax.mail.Folder;
import javax.mail.Message;
import javax.mail.Multipart;
import javax.mail.Part;
import javax.mail.Session;
import javax.mail.Store;
import javax.mail.internet.InternetAddress;
import javax.mail.internet.MimeBodyPart;
import javax.mail.internet.MimeMessage.RecipientType;

import org.apache.commons.lang3.StringUtils;

public class LHWorkOrderProcessor {
	public static final String NEW_LINE_DELIMITER = "<NEW_LINE_DELIMITER>";

	public static void main(String[] args) throws Exception {
		String configFile = args[0];
		LHCommonConstants.init(configFile);
		receiveWOMails();
	}

	/**
	 * this method will retrieve and read the emails from the INBOX
	 * 
	 * @param popServer
	 * @param popUser
	 * @param popPassword
	 */
	public static void receiveWOMails() {
		Store store = null;
		Folder folder = null;
		Folder junkFolder = null;
		String popServer = LHCommonConstants.getLh_mail_smtp_commenting_host();
		String popUser;
		String popPassword;
		String type;
		String emailId=null;
		String[] emailArray = null;

		try {
			// -- Get hold of the default session --
			Properties props = System.getProperties();
			Session session = Session.getDefaultInstance(props, null);
			session.setDebug(false);

			for (String emailPwd : LHCommonConstants.getLh_wo_email_list()) {
				emailArray = emailPwd.split(":");
				type = emailArray[0];
				emailId = emailArray[1];
				popUser = emailArray[2];
				popPassword = emailArray[3];

				// -- Get hold of a POP3 message store, and connect to it --
				// store = session.getStore("pop3");
				store = session.getStore("imap");
				store.connect(popServer, popUser, popPassword);

				// -- Try to get hold of the default folder --
				folder = store.getDefaultFolder();
				if (folder == null) throw new Exception("No default folder");

				// -- ...and its INBOX --
				folder = folder.getFolder("INBOX");
				if (folder == null) throw new Exception("No POP3 INBOX");

			    junkFolder = store.getFolder("JUNK E-MAIL");
			    if (!junkFolder.exists()) throw new Exception("No Junk box");
					
				// -- Open the folder for read only --
				// folder.open(Folder.READ_ONLY);
				folder.open(Folder.READ_WRITE);

				// -- Get the message wrappers and process them --
				Message[] msgs = folder.getMessages();

				System.out.println(type + " -- EmailReader" + " msgs "+ msgs.length);
				/**Start processing the messages from INBOX */
				for (int msgNum = 0; msgNum < msgs.length; msgNum++) {
					Address[] toArr = msgs[msgNum].getRecipients(RecipientType.TO);
					
					/**Process only if the email is specifically addressed to the LH Email Address, 
					 * And Subject is present
					 * And the Subject doesn't have a WO number in it*/
					if(toArr!=null && toArr.length==1 && emailId.equalsIgnoreCase(((InternetAddress)toArr[0]).getAddress())
							&& StringUtils.isNotBlank(msgs[msgNum].getSubject())
							&& "-100".equals(LHCommonUtils.getWorkOrderId(msgs[msgNum].getSubject()))){
						processWorkOrderMessage(msgs[msgNum], type);
					}
					else{
						/**Move invalid/ignored mails to Junk and Remove them from Inbox*/
						System.out.println("Fail : "+msgs[msgNum].getSubject());
						folder.copyMessages(new Message[]{msgs[msgNum]}, junkFolder);
						msgs[msgNum].setFlag(Flags.Flag.DELETED, true);
					}
				}

				if (folder != null)
					folder.close(true);
				if (store != null)
					store.close();
			}

		} catch (Exception ex) {
			ex.printStackTrace();
		} finally {
			try {
				if (folder != null && folder.isOpen())
					folder.close(true);
				if (store != null)
					store.close();
			} catch (Exception ex2) {
				ex2.printStackTrace();
			}
		}
	}

	public static void processWorkOrderMessage(Message message, String type) throws Exception {
		Pattern woDatePattern = Pattern.compile("^(\\s*?)date(\\s*?):(.*?)", Pattern.CASE_INSENSITIVE);
		Pattern woProjCodePattern = Pattern.compile("^(\\s*?)Project(\\s*?):(.*?)", Pattern.CASE_INSENSITIVE);
		List<Part> attachmentParts = new ArrayList<Part>();
		int lineCounter = 0;
		String contentType = null;
		String messageId = null;
		String encoding = "UTF-8";
		BufferedReader reader = null;
		String NL = System.getProperty("line.separator");
		Part messagePart = message;
		Object content = messagePart.getContent();
		LHWorkOrder workOrder = new LHWorkOrder();
		
		messageId = message.getHeader("Message-ID")[0];
		
		workOrder.setType(type.toUpperCase());
		
		StringBuffer ccList = new StringBuffer();
		ccList = LHCommonUtils.getWorkOrderCcList(message.getRecipients(RecipientType.CC), ccList);
		ccList = LHCommonUtils.getWorkOrderCcList(message.getRecipients(RecipientType.BCC), ccList);
		workOrder.setCcList(ccList.toString());
		
		workOrder.setEmailId(((InternetAddress) message.getFrom()[0]).getAddress());
		/**Parse out the subject*/
		String subject = message.getSubject();
		System.out.println("SUBJECT: " + subject);
		workOrder.setTitle(LHCommonUtils.getWorkorderTitle(subject));
		
		/**Check Criticality/Severity based on request type*/
		if (type.equalsIgnoreCase("REQUEST")) {
			workOrder.setCriticality(LHCommonUtils.isCritical(subject));
		}

		if (type.equalsIgnoreCase("PROBLEM")) {
			workOrder.setSeverity(LHCommonUtils.getSeverity(subject));
		}
		
		/**Start processing the Message Body*/
		if (content instanceof Multipart) {
			messagePart = ((Multipart) content).getBodyPart(0);
			for (int i = 1, n = ((Multipart) content).getCount(); i < n; i++) {
				Part part = ((Multipart) content).getBodyPart(i);
				String disposition = part.getDisposition();
				if ((disposition != null)&& ((disposition.equals(Part.ATTACHMENT) || (disposition.equals(Part.INLINE))))) {
					if (disposition.equals(Part.INLINE) && part.isMimeType("image/*")) {
						if (part.getSize() > LHCommonConstants.getLh_mail_signature_sizelimit()) {
							attachmentParts.add(part);
						}
					} else {
						attachmentParts.add(part);
					}
				} else if (disposition == null) {
					MimeBodyPart mbp = (MimeBodyPart) part;
					if (mbp.isMimeType("image/*")) {
						if (part.getSize() > LHCommonConstants.getLh_mail_signature_sizelimit()) {
							attachmentParts.add(part);
						}
					}
				}
			}
			if (!messagePart.isMimeType("multipart/alternative")) {
				messagePart = LHCommonUtils.processMultiPartMsg(messagePart, attachmentParts);
			}
			workOrder.setAttachments(attachmentParts);
		}
		
		contentType = messagePart.getContentType();
		System.out.println("CONTENT TYPE " + contentType);
		if (contentType != null && contentType.indexOf("charset=") > -1) {
			encoding = contentType.substring(contentType.indexOf("charset=") + 8).trim();
		}

		if (!messagePart.isMimeType("multipart/mixed")) {
			String currentLine = "";
			if (contentType.toLowerCase().startsWith("text/plain") || contentType.toLowerCase().startsWith("text/html")) {
				reader = new BufferedReader(new InputStreamReader(messagePart.getInputStream(), encoding));
				currentLine = reader.readLine();
				StringBuilder sb = new StringBuilder();

				String desc = "";
				lineCounter = 0;
				while (currentLine != null) {
					if(lineCounter < 1){
						String dateValueArr[] = woDatePattern.split(currentLine);
						String prjCodeValArr[] = woProjCodePattern.split(currentLine);
					
						if(dateValueArr.length>1 && lineCounter < 1){
							workOrder.setDate(LHCommonUtils.parseWorkOrderDate(dateValueArr[1].trim()));
						}
						else if(prjCodeValArr.length>1 && lineCounter < 1){
							workOrder.setProject(prjCodeValArr[1].trim());
						}
						else{
							if(StringUtils.isNotBlank(currentLine)){
								lineCounter++;
							}
							sb.append(currentLine);
							sb.append(NEW_LINE_DELIMITER);
						}
					}
					else {
						sb.append(currentLine);
						sb.append(NEW_LINE_DELIMITER);
					}
					currentLine = reader.readLine();
				}
				desc = sb.toString().replaceAll(NEW_LINE_DELIMITER, NL);
				desc = StringUtils.stripEnd(desc, NL).trim() + NL + NL;
				workOrder.setDescription(desc);
				/*System.out.println("Project:" + workOrder.getProject());
				System.out.println("Site:" + workOrder.getSite());
				System.out.println("URL:" + workOrder.getUrl());
				System.out.println("Date:" + workOrder.getDate());
				System.out.println("Email:" + workOrder.getEmailId());
				System.out.println("Critical:" + workOrder.getCriticality());
				System.out.println("Severity:" + workOrder.getSeverity());
				System.out.println("Title:" + workOrder.getTitle());
				System.out.println("Description:" + workOrder.getDescription());*/
			}
			else if (messagePart.isMimeType("multipart/alternative")) {
				Multipart mp = (Multipart) messagePart.getContent();
				reader = new BufferedReader(new InputStreamReader(mp.getBodyPart(0).getInputStream(), encoding));
				currentLine = reader.readLine();
				StringBuilder sb = new StringBuilder();

				String desc = "";
				while (currentLine != null) {
					if(lineCounter < 1){
						String dateValueArr[] = woDatePattern.split(currentLine);
						String prjCodeValArr[] = woProjCodePattern.split(currentLine);
					
						if(dateValueArr.length>1 && lineCounter < 1){
							workOrder.setDate(LHCommonUtils.parseWorkOrderDate(dateValueArr[1].trim()));
						}
						else if(prjCodeValArr.length>1 && lineCounter < 1){
							workOrder.setProject(prjCodeValArr[1].trim());
						}
						else{
							if(StringUtils.isNotBlank(currentLine)){
								lineCounter++;
							}
							sb.append(currentLine);
							sb.append(NEW_LINE_DELIMITER);
						}
					}
					else {
						sb.append(currentLine);
						sb.append(NEW_LINE_DELIMITER);
					}
					currentLine = reader.readLine();
				}
				desc = sb.toString().replaceAll(NEW_LINE_DELIMITER, NL);
				desc = StringUtils.stripEnd(desc, NL).trim() + NL + NL;
				workOrder.setDescription(desc);
				/*System.out.println("Project:" + workOrder.getProject());
				System.out.println("Site:" + workOrder.getSite());
				System.out.println("URL:" + workOrder.getUrl());
				System.out.println("Date:" + workOrder.getDate());
				System.out.println("Email:" + workOrder.getEmailId());
				System.out.println("Critical:" + workOrder.getCriticality());
				System.out.println("Severity:" + workOrder.getSeverity());
				System.out.println("Title:" + workOrder.getTitle());
				System.out.println("Description:" + workOrder.getDescription());*/
			}
		}
		/**If Message has exceeded 6 retries, skip it.*/
		if(LHEmailMetadataSerializer.getMessageCount(messageId)<6){
			String status = LHWorkOrderCreateHandler.createWorkOrder(workOrder);
			status = status.substring(status.length() - 7, status.length());
			System.out.println("status :" + status);
			/**After success delete Message*/
      	  	if (status.trim().contains(LHCommonConstants.getLh_comment_service_success())){
      		  message.setFlag(Flags.Flag.DELETED, true); 
            }else{
            	/**On Failure add to the tracking DB - it marks the retry count*/
          	  	LHEmailMetadataSerializer.addMessage(messageId);
          	  	LHEmailMetadataSerializer.serialize();
            }
        }else{
      	  System.out.println("No of attempts to process the email with id "+messageId+" exceeded the maximum limit. Please take manual action now");
        }
	}
}
