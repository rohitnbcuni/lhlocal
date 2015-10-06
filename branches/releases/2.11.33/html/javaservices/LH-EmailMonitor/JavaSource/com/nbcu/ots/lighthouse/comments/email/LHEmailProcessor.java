package com.nbcu.ots.lighthouse.comments.email;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.Reader;
import java.util.ArrayList;
import java.util.LinkedList;
import java.util.List;
import java.util.Properties;

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

import org.apache.commons.lang3.StringUtils;

public class LHEmailProcessor {
	
	public static final String NEW_LINE_DELIMITER="<NEW_LINE_DELIMITER>";

      /**
       * @param args
       * popServer - the url of the POP3 server eg. pop.iprimus.com.au
       * popUser - POP server user name
       * popPassword - password associated with the above username
       */
      public static void main(String[] args) {
            // TODO Auto-generated method stub
          try {
            	String configFile = args[0];
            	LHCommonConstants.init(configFile);
            	LHEmailMetadataSerializer.deserialize();
            	String popServer=LHCommonConstants.getLh_mail_smtp_host();//args[0];
                String popUser=LHCommonConstants.getLh_mail_smtp_user();//args[1];
                String popPassword=LHCommonConstants.getLh_mail_smtp_pwd();//args[2];
                System.out.println("java LHEmailProcessor "+configFile);
                
                receive(popServer, popUser, popPassword);
                
                /**Workorder Creation Part*/
                LHWorkOrderProcessor.receiveWOMails();
          }
          catch (Exception ex) {
                System.out.println("Usage: LHEmailProcessor"+" configFile");
          }
          System.exit(0);
      }
      
      /**
       * this method will retrieve and read the emails from the INBOX
       * @param popServer
       * @param popUser
       * @param popPassword
       */
      public static void receive(String popServer, String popUser, String popPassword)
      {

            Store store=null;
            Folder folder=null;

          try
          {
                // -- Get hold of the default session --
                Properties props = System.getProperties();
                Session session = Session.getDefaultInstance(props, null);
                session.setDebug(false);
      
                // -- Get hold of a POP3 message store, and connect to it --
                //store = session.getStore("pop3");
                store = session.getStore("imap");
                store.connect(popServer, popUser, popPassword);
        
                // -- Try to get hold of the default folder --
                folder = store.getDefaultFolder();
                if (folder == null) throw new Exception("No default folder");
      
                // -- ...and its INBOX --
                folder = folder.getFolder("INBOX");
                if (folder == null) throw new Exception("No POP3 INBOX");
      
                // -- Open the folder for read only --
               // folder.open(Folder.READ_ONLY);
                folder.open(Folder.READ_WRITE);
      
                // -- Get the message wrappers and process them --
                Message[] msgs = folder.getMessages();
                
                System.out.println("EmailReader"
                            +" msgs " + msgs.length);
                
                for (int msgNum = 0; msgNum < msgs.length; msgNum++)
                {
                      processMessage(msgs[msgNum]);
                      //processMessage(msgs[msgNum]);
                }
      
          }
          catch (Exception ex)
          {
                ex.printStackTrace();
          }
          finally
          {
                // -- Close down nicely --
                try
                {
                      if (folder!=null) folder.close(true);
                      if (store!=null) store.close();
                }
                catch (Exception ex2) {ex2.printStackTrace();}
          }
      }
      
      /**
       * this method will print the message
       * @param message
       */
      public static void processMessage(Message message)
      {
    	  StringBuilder sb = new StringBuilder();  
    	  InputStream is = null;
    	  BufferedReader reader = null;
    	  byte[] utf8 = null;
    	  String contentBody = "";
    	  String messageId = null;
    	  String userAgent = null;
    	  String encoding = "UTF-8";
    	  String NL = System.getProperty("line.separator");
    	  List<Part> attachmentParts = new ArrayList<Part>();
    	  
    	  try
            {
                  // Get the header information
                  String personal =((InternetAddress)message.getFrom()[0]).getPersonal();
                  //if (from==null) 
                  String from=((InternetAddress)message.getFrom()[0]).getAddress();
                  System.out.println("FROM: "+from);
                  
                  String subject=message.getSubject();
                  System.out.println("SUBJECT: "+subject);
                  
                  //String dateTime = message.getSentDate().toString();
                  System.out.println("DATE: "+ message.getSentDate());
                  
                  // -- Get the message part (i.e. the message itself) --
                  Part messagePart=message;
                  Object content=messagePart.getContent();
                  
                  // -- or its first body part if it is a multipart message --
                  if (content instanceof Multipart)
                  {
                        messagePart=((Multipart)content).getBodyPart(0);
                        for (int i=1, n=((Multipart)content).getCount(); i<n; i++) {
                            Part part = ((Multipart)content).getBodyPart(i);
                            String disposition = part.getDisposition();
                            if ((disposition != null) && ((disposition.equals(Part.ATTACHMENT) || (disposition.equals(Part.INLINE))))) {
                            	if(disposition.equals(Part.INLINE) && part.isMimeType("image/*")) {
                            		if(part.getSize() > LHCommonConstants.getLh_mail_signature_sizelimit()) {
                            			attachmentParts.add(part);
                            		}
                          	  	}
                            	else{
                            		attachmentParts.add(part);
                            	}
                            }
                            else if(disposition==null) {
                            	MimeBodyPart mbp = (MimeBodyPart)part;
                            	if (mbp.isMimeType("image/*")) {
                            		if(part.getSize()>LHCommonConstants.getLh_mail_signature_sizelimit()){
                              	  		attachmentParts.add(part);
                              	  	}
                            	}
                            }
                        }
                        if(!messagePart.isMimeType("multipart/alternative")){
                        	messagePart = processMultiPartMsg(messagePart, attachmentParts);
                        }
                        System.out.println("[ Multipart Message ]");
                  }

                  // -- Get the content type --
                  String contentType=messagePart.getContentType();

                  // -- If the content is plain text, we can print it --
                  System.out.println("CONTENT:"+contentType);
                  
                  if (contentType!=null && contentType.indexOf("charset=")>-1){
                	  encoding = contentType.substring(contentType.indexOf("charset=")+8).trim(); 
                	  System.out.println(encoding);
                  }
                  
                  
                  messageId = message.getHeader("Message-ID")[0];
                  System.out.println(messageId);

                  if (contentType.toLowerCase().startsWith("text/plain") || contentType.toLowerCase().startsWith("text/html"))
                  {
                	   reader = new BufferedReader(new InputStreamReader(messagePart.getInputStream(), encoding));
                        String currentLine =reader.readLine();
                        while (currentLine!=null)
                        {   
                              sb.append(currentLine); 
                           	  sb.append(NEW_LINE_DELIMITER);
                              currentLine=reader.readLine();                             
                        }
                  }
                  else if(messagePart.isMimeType("multipart/alternative")){
                	  Multipart mp = (Multipart)messagePart.getContent();  
                      reader = new BufferedReader(new InputStreamReader(mp.getBodyPart(0).getInputStream(), encoding));
                      String currentLine =reader.readLine();
                        while (currentLine!=null)
                        {
                        	 sb.append(currentLine); 
                          	  sb.append(NEW_LINE_DELIMITER);
                             currentLine=reader.readLine();   
                        }
                  }
                  
                  String commentBody =  sb.toString().trim();
                  boolean commentFilterOn = true;
                  
                  if (commentFilterOn){
                	  
                	  commentBody = LHCommentFilter.filterComment(commentBody);
                  }
                  commentBody = commentBody.replaceAll(NEW_LINE_DELIMITER,NL);
                  commentBody = StringUtils.stripEnd(commentBody,NL).trim()+NL+NL;
                  System.out.println("CommentBody after filtering:"+commentBody);
                  System.out.println("Attachment Count : "+(attachmentParts!=null?attachmentParts.size():"0"));
                  if(LHEmailMetadataSerializer.getMessageCount(messageId)<6){
                	  String status = null;
                	  status = LHCommentHandler.postMessage(from,subject,commentBody, attachmentParts);
                	  System.out.println("status: "+status);
                  
                	  if (status.trim().contains(LHCommonConstants.getLh_comment_service_success())){
                		  message.setFlag(Flags.Flag.DELETED, true); 
	                  }else{
	                	  LHEmailMetadataSerializer.addMessage(messageId);
	                	  LHEmailMetadataSerializer.serialize();
	                  }
                  }else{
                	  System.out.println("No of attempts to process the email with id "+messageId+" exceeded the maximum limit. Please take manual action now");
                  }
                  System.out.println("-----------------------------");
            }
            catch (Exception ex)
            {
                  ex.printStackTrace();
            }finally {
            	
            	 // -- Close down nicely --
                try
                {
                      if (reader!=null) reader.close();
                      if (is!=null) is.close();
                }
                catch (Exception ex2) {ex2.printStackTrace();}
            	
            }
      }
      
      private static Part processMultiPartMsg(Part messagePart, List<Part> attachmentParts) throws Exception {
    	  Part tempMsgPart = null;
    	  
    	  if(messagePart.isMimeType("text/*")){
    		  tempMsgPart = messagePart;
    	  }
    	  else if(messagePart.isMimeType("multipart/alternative")){
    		  tempMsgPart = messagePart;
    	  }
    	  else if (messagePart.isMimeType("multipart/*")){
    		  Multipart mPart = (Multipart) messagePart.getContent();  
              int partCount = mPart.getCount();  
              for ( int i = 0; i < partCount ; i++ ) {  
            	  Part tempMsgPartRec = processMultiPartMsg(mPart.getBodyPart(i), attachmentParts);
            	  if(tempMsgPartRec!=null){
            		  tempMsgPart = tempMsgPartRec;
            	  }
              }  
    	  }
    	  else{
                  String disposition = messagePart.getDisposition();
                  if ((disposition != null) && ((disposition.equals(Part.ATTACHMENT) || (disposition.equals(Part.INLINE))))){
                	  if(disposition.equals(Part.INLINE) && messagePart.isMimeType("image/*") ){
                  			if(messagePart.getSize() > LHCommonConstants.getLh_mail_signature_sizelimit()) {
                  				attachmentParts.add(messagePart);
                  			}
                	  	}
	                  	else{
	                  		attachmentParts.add(messagePart);
	                  	}
                  }
                  else if(disposition==null){
                  	MimeBodyPart mbp = (MimeBodyPart)messagePart;
                  	if (mbp.isMimeType("image/*")) {
                  		if(messagePart.getSize()>LHCommonConstants.getLh_mail_signature_sizelimit()){
                  			attachmentParts.add(messagePart);
                  		}
                  	}
                  }
    	  }
    	  return tempMsgPart;
      }

      /**
       * this method will process an email message and write
       * relevant contents into the xml file
       * @param message
       */
      private static void processMessage1(Message message)
      {
            try
            {
                  // -- Get the message part (i.e. the message itself) --
                  Part messagePart=message;
                  Object content=messagePart.getContent();
            
                  // -- or its first body part if it is a multipart message --
                  if (content instanceof Multipart)
                  {
                        messagePart=((Multipart)content).getBodyPart(0);
                  }
                  
                  // -- Get the content type --
                  String contentType=messagePart.getContentType();
                  
                  if (contentType.startsWith("text/plain") || contentType.startsWith("text/html"))
                  {
                        InputStream is = messagePart.getInputStream();

                        BufferedReader reader = new BufferedReader(new InputStreamReader(is));
                                                
                        
                        // now read the message into a List - each message line is a list item
                        // a List will be easier to manipulate and its indexed
                        // remove blank lines at the same time
                        List msgList = new LinkedList();
                        
                        String thisLine=reader.readLine();
                        while (thisLine!=null)
                        {
                              if(thisLine.trim().length()>0)
                                    msgList.add(thisLine);
                              thisLine=reader.readLine();
                        }
                        
                  }
            }
            catch(Exception x)
            {
                  x.printStackTrace();
            }
            
      }
      
      static String readInput(InputStream is) {
    	    StringBuffer buffer = new StringBuffer();
    	    try {
    	        //FileInputStream fis = new FileInputStream("test.txt");
    	        InputStreamReader isr = new InputStreamReader(is, "UTF8");
    	        Reader in = new BufferedReader(isr);
    	        int ch;
    	        while ((ch = in.read()) > -1) {
    	            buffer.append((char)ch);
    	        }
    	        in.close();
    	        return buffer.toString();
    	    } 
    	    catch (IOException e) {
    	        e.printStackTrace();
    	        return null;
    	    }
    	}
       
}



