package com.nbcu.ots.lighthouse.comments.email;

import java.io.BufferedReader;
import java.io.DataInputStream;
import java.io.DataOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.InetAddress;
import java.net.InetSocketAddress;
import java.net.Proxy;
import java.net.URL;
import java.net.URLEncoder;
import java.net.UnknownHostException;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.List;

import javax.mail.MessagingException;
import javax.mail.Part;

import org.apache.commons.io.IOUtils;
import org.apache.commons.lang3.StringUtils;

import com.sun.net.ssl.HttpsURLConnection;

public class LHCommentHandler {

	public static void main(String[] args) throws Exception{
		String s = "RE: WO 29602: Comment - Submit a Request - Please make the following vip and dns changes on dev";
        String from = "santhosh.kuriakose@nbcuni.com";
		//String status = postMessage(from,s,"Test again 123",);
		//System.out.println(status);
	}
		
	public static synchronized String postMessage(String from,String subject, String body,List<Part>  attachmentParts){
		String status = "ERR100";
		
		String messageId = LHCommonUtils.getWorkOrderId(subject);
		if (messageId.equals("-100")) return status;
		long currentTime = System.currentTimeMillis();
		String tokenInput = from+"|"+messageId+"|"+getHostName()+"|"+currentTime;
		String fileName =null;
		byte[] fileAttachment =null;
		//File binaryFile = fileAttachment.;
		String lineEnd = "\r\n";
		String twoHyphens = "--";
		String boundary = "*****";
		BufferedReader rd = null;
		StringBuilder sb = null;
		String line = null;
		HttpURLConnection connection = null;
		SimpleDateFormat sdf = new SimpleDateFormat("MM-dd-yyyy-kkmmss-");
		
		/**Set up the environment variables for connecting to the Server*/
		System.getProperties().put("http.proxyHost", LHCommonConstants.getLh_http_proxy_host()); 
		System.getProperties().put("http.proxyPort", LHCommonConstants.getLh_http_proxy_port()); 
		System.getProperties().put("https.proxyHost", LHCommonConstants.getLh_https_proxy_host()); 
		System.getProperties().put("https.proxyPort", LHCommonConstants.getLh_https_proxy_port()); 
		System.setProperty("java.protocol.handler.pkgs", "com.sun.net.ssl.internal.www.protocol");
		java.security.Security.addProvider(new com.sun.net.ssl.internal.ssl.Provider()); 
		//String serviceURL = "http://dev3.lighthouse.nbcuots.com/services/commentServiceAttachment.php";
		
		DataOutputStream output=null;
		try {
			String serviceURL = LHCommonConstants.getLh_comment_service_url();
			System.out.println("serviceURL : "+serviceURL);
			URL myurl = new URL(serviceURL);		
			/*Proxy proxy = new Proxy(Proxy.Type.HTTP, new InetSocketAddress(
					"localhost", 1540));*/
			if (serviceURL.toLowerCase().startsWith("https:")){
				connection = (HttpsURLConnection)myurl.openConnection(); 
			}else{
				connection = (HttpURLConnection)myurl.openConnection(); 
			}
			connection.setFollowRedirects(false);
			//System.out.println(connection.getContentEncoding());
			connection.setRequestProperty("Content-Type","multipart/form-data; boundary=" + boundary); 
			connection.setRequestProperty("User-Agent", "Mozilla/5.0 (Windows NT 5.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1");  
			connection.setDoOutput(true);  
			connection.setDoInput(true);  
			connection.setRequestMethod("POST");  
			output = new DataOutputStream(connection.getOutputStream());  
			System.out.println("Obtained Connection....");
			output.writeBytes(twoHyphens + boundary + lineEnd);
			output.writeBytes("Content-Disposition: form-data; name=\"lh_email\""+ lineEnd + lineEnd);
			output.writeBytes(from + lineEnd);
			
			output.writeBytes(twoHyphens + boundary + lineEnd);
			output.writeBytes("Content-Disposition: form-data; name=\"lh_token\""+ lineEnd + lineEnd);
			output.writeBytes(LHCommentTokenGenerator.generateToken(tokenInput) + lineEnd);
			
			output.writeBytes(twoHyphens + boundary + lineEnd);
			output.writeBytes("Content-Disposition: form-data; name=\"source_host_name\""+ lineEnd + lineEnd);
			output.writeBytes(getHostName() + lineEnd);
			
			output.writeBytes(twoHyphens + boundary + lineEnd);
			output.writeBytes("Content-Disposition: form-data; name=\"lh_utc_time\""+ lineEnd + lineEnd);
			output.writeBytes(currentTime + lineEnd);

			output.writeBytes(twoHyphens + boundary + lineEnd);
			output.writeBytes("Content-Disposition: form-data; name=\"lh_wid\""+ lineEnd + lineEnd);
			output.writeBytes(LHCommonUtils.getWorkOrderId(subject) + lineEnd);
			System.out.println("comment before passing:"+URLEncoder.encode(body, "UTF-8"));
			output.writeBytes(twoHyphens + boundary + lineEnd);
			output.writeBytes("Content-Disposition: form-data; name=\"lh_comment\""+ lineEnd + lineEnd);
			output.writeBytes(URLEncoder.encode(body, "UTF-8")+ lineEnd);

			output.writeBytes(twoHyphens + boundary + lineEnd);
			output.writeBytes("Content-Disposition: form-data; name=\"lh_subject\""+ lineEnd + lineEnd);
			output.writeBytes(subject + lineEnd);
			
				if(attachmentParts!=null && attachmentParts.size()>0){
					for(Part attachmentPart : attachmentParts){
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
			output.writeBytes("Content-Disposition: form-data; name=\"lh_submit\""+ lineEnd + lineEnd);
			output.writeBytes("Submit Query" + lineEnd);
			output.writeBytes(twoHyphens + boundary + lineEnd + "--");
			output.flush();
			output.close();
			
			/**Gather the response from the service API*/
			rd = new BufferedReader(new InputStreamReader(connection.getInputStream()));
			sb = new StringBuilder();

			while ((line = rd.readLine()) != null) {
				sb.append(line + '\n');
			}
			System.out.println("Resp Code:"+connection .getResponseCode());  
			System.out.println("Resp Message:"+ connection .getResponseMessage()); 
			System.out.println(sb.toString());
		}
		catch (IOException e) {
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
		return sb.toString();
	
	//	return status;
	}

	public static String getHostName(){
		String hostName = "localhost";
		try {
		    InetAddress addr = InetAddress.getLocalHost();
		    // Get IP Address
		    byte[] ipAddr = addr.getAddress();
		    // Get hostname
		    hostName = addr.getHostName();
		} catch (UnknownHostException e) {
			e.printStackTrace();
		}
		return hostName;
	}
}
