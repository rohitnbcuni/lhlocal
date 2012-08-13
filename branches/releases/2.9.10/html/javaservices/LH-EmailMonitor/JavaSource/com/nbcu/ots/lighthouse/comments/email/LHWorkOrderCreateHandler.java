package com.nbcu.ots.lighthouse.comments.email;

import java.io.BufferedReader;
import java.io.DataOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLEncoder;
import java.text.SimpleDateFormat;
import java.util.Date;

import javax.mail.MessagingException;
import javax.mail.Part;

import org.apache.commons.io.IOUtils;
import org.apache.commons.lang3.StringUtils;

import com.sun.net.ssl.HttpsURLConnection;

public class LHWorkOrderCreateHandler {
	
	public static String createWorkOrder(LHWorkOrder workOrder) throws MalformedURLException, IOException {
		String fileName =null;
		byte[] fileAttachment =null;
		long currentTime = System.currentTimeMillis();
		String tokenInput = workOrder.getEmailId()+"|"+LHCommentHandler.getHostName()+"|"+currentTime;
		String lineEnd = "\r\n";
		String twoHyphens = "--";
		String boundary = "*****";
		BufferedReader rd = null;
		StringBuilder sb = null;
		String line = null;
		String status="";
		HttpURLConnection connection = null;
		SimpleDateFormat sdf = new SimpleDateFormat("MM-dd-yyyy-kkmmss-");
		
		System.getProperties().put("http.proxyHost", LHCommonConstants.getLh_http_proxy_host()); 
		System.getProperties().put("http.proxyPort", LHCommonConstants.getLh_http_proxy_port()); 
		System.getProperties().put("https.proxyHost", LHCommonConstants.getLh_https_proxy_host()); 
		System.getProperties().put("https.proxyPort", LHCommonConstants.getLh_https_proxy_port()); 
		System.setProperty("java.protocol.handler.pkgs", "com.sun.net.ssl.internal.www.protocol");
		java.security.Security.addProvider(new com.sun.net.ssl.internal.ssl.Provider()); 
		

		String serviceURL = LHCommonConstants.getLh_comment_service_workorder_url();
		System.out.println("serviceURL : "+serviceURL);
		URL myurl = new URL(serviceURL);		
		if (serviceURL.toLowerCase().startsWith("https:")){
			connection = (HttpsURLConnection)myurl.openConnection(); 
		}else{
			connection = (HttpURLConnection)myurl.openConnection(); 
		}
		connection.setFollowRedirects(false);
		connection.setRequestProperty("Content-Type","multipart/form-data; boundary=" + boundary); 
		connection.setRequestProperty("User-Agent", "Mozilla/5.0 (Windows NT 5.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1");  
		connection.setDoOutput(true);  
		connection.setDoInput(true);  
		connection.setRequestMethod("POST");  
		//InputStream input = null;
			DataOutputStream output = new DataOutputStream(connection.getOutputStream());     
			try {
		
		//DataOutputStream output = new DataOutputStream(connection.getOutputStream());     
			System.out.println("Obtained Connetion....");
			output.writeBytes(twoHyphens + boundary + lineEnd);
			output.writeBytes("Content-Disposition: form-data; name=\"lh_email\""+ lineEnd + lineEnd);
			output.writeBytes(workOrder.getEmailId() + lineEnd);

			output.writeBytes(twoHyphens + boundary + lineEnd);
			output.writeBytes("Content-Disposition: form-data; name=\"lh_type\""+ lineEnd + lineEnd);
			output.writeBytes(workOrder.getType() + lineEnd);
			
			output.writeBytes(twoHyphens + boundary + lineEnd);
			output.writeBytes("Content-Disposition: form-data; name=\"lh_token\""+ lineEnd + lineEnd);
			output.writeBytes(LHCommentTokenGenerator.generateToken(tokenInput) + lineEnd);
			
			output.writeBytes(twoHyphens + boundary + lineEnd);
			output.writeBytes("Content-Disposition: form-data; name=\"lh_utc_time\""+ lineEnd + lineEnd);
			output.writeBytes(currentTime + lineEnd);
			
			output.writeBytes(twoHyphens + boundary + lineEnd);
			output.writeBytes("Content-Disposition: form-data; name=\"source_host_name\""+ lineEnd + lineEnd);
			output.writeBytes(LHCommentHandler.getHostName() + lineEnd);
			
			output.writeBytes(twoHyphens + boundary + lineEnd);
			output.writeBytes("Content-Disposition: form-data; name=\"woTitle\""+ lineEnd + lineEnd);
			output.writeBytes(URLEncoder.encode(workOrder.getTitle(),"UTF-8")	 + lineEnd);
			
			output.writeBytes(twoHyphens + boundary + lineEnd);
			output.writeBytes("Content-Disposition: form-data; name=\"woDesc\""+ lineEnd + lineEnd);
			output.writeBytes(URLEncoder.encode(workOrder.getDescription(),"UTF-8") + lineEnd);

			output.writeBytes(twoHyphens + boundary + lineEnd);
			output.writeBytes("Content-Disposition: form-data; name=\"ccList\""+ lineEnd + lineEnd);
			output.writeBytes(workOrder.getCcList() + lineEnd);
			
			output.writeBytes(twoHyphens + boundary + lineEnd);
			output.writeBytes("Content-Disposition: form-data; name=\"lh_severity\""+ lineEnd + lineEnd);
			output.writeBytes(workOrder.getSeverity() + lineEnd);
			
			if(workOrder.getCriticality()){
				output.writeBytes(twoHyphens + boundary + lineEnd);
				output.writeBytes("Content-Disposition: form-data; name=\"lh_critical\""+ lineEnd + lineEnd +"1"+ lineEnd);
			}
			if(workOrder.getDate()!=null){
				output.writeBytes(twoHyphens + boundary + lineEnd);
				output.writeBytes("Content-Disposition: form-data; name=\"required_date\""+ lineEnd + lineEnd);
				output.writeBytes(workOrder.getDate() + lineEnd);
			}
			if(workOrder.getProject()!=null){
				output.writeBytes(twoHyphens + boundary + lineEnd);
				output.writeBytes("Content-Disposition: form-data; name=\"lh_project\""+ lineEnd + lineEnd);
				output.writeBytes(workOrder.getProject() + lineEnd);
			}
			
			if(workOrder.getAttachments()!=null && workOrder.getAttachments().size()>0){
				for(Part attachmentPart : workOrder.getAttachments()){
					if(attachmentPart.getDisposition()==null){
						//Give unique file name to embedded images
						fileName = sdf.format(new Date())+attachmentPart.getFileName();
					}
					else{
						fileName = attachmentPart.getFileName();
					}
					fileAttachment = IOUtils.toByteArray(attachmentPart.getInputStream());

					if(StringUtils.isNotBlank(fileName)){
						System.out.println("fileName -:- "+fileName+"\n"+fileAttachment);
						InputStream input = null;
						try {
							output.writeBytes(twoHyphens + boundary + lineEnd);
							output.writeBytes("Content-Disposition: attachment; name=\"upload_file[]\";filename=\""+ fileName + "\"" + lineEnd);
							//output.writeBytes("Content-Type: "+fileType + lineEnd + lineEnd);
							output.writeBytes(lineEnd);
							
							output.write(fileAttachment);
							output.writeBytes(lineEnd);
							output.writeBytes(twoHyphens + boundary + lineEnd);
						} catch (IOException e) {
							e.printStackTrace();
						} finally {
							if (input != null)
								try {
									input.close();
								} catch (IOException logOrIgnore) {
								}
						}
					}
				}
			}
			else
			{
				output.writeBytes(twoHyphens + boundary + lineEnd);
				output.writeBytes("Content-Disposition: attachment; name=\"upload_file[]\";filename=\"\"" + lineEnd);
				output.writeBytes(lineEnd);
				output.writeBytes(twoHyphens + boundary + lineEnd);
			}
			//output.write(fileAttachment);
			output.writeBytes("Content-Disposition: form-data; name=\"lh_submit\""+ lineEnd + lineEnd);
			output.writeBytes("Submit Query" + lineEnd);
			output.writeBytes(twoHyphens + boundary + lineEnd + "--");
			output.flush();
			output.close();
			} catch (IOException e) {
				e.printStackTrace();
			} catch (MessagingException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
			finally {
				try {
					if(output!=null)output.close();
					if (rd!=null)rd.close();
					if (connection!=null) connection.disconnect();
				}catch (Exception e){
					e.printStackTrace();
				}
			}

			// read the result from the server
			try {
				rd = new BufferedReader(new InputStreamReader(connection
						.getInputStream()));
				sb = new StringBuilder();

				while ((line = rd.readLine()) != null) {
					sb.append(line + '\n');
				}
				status=sb.toString();
				System.out.println(sb.toString());
			} catch (IOException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
			finally {
				try {
					if(output!=null)output.close();
					if (rd!=null)rd.close();
					if (connection!=null) connection.disconnect();
				}catch (Exception e){
					e.printStackTrace();
				}
			}
			return status;
	}

}
