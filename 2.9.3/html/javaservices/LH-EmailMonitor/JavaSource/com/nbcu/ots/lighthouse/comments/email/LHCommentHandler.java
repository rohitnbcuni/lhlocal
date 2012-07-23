package com.nbcu.ots.lighthouse.comments.email;

import java.io.DataInputStream;
import java.io.DataOutputStream;
import java.net.HttpURLConnection;
import java.net.InetAddress;
import java.net.URL;
import java.net.URLEncoder;
import java.net.UnknownHostException;

import com.sun.net.ssl.HttpsURLConnection;

public class LHCommentHandler {

	public static void main(String[] args) throws Exception{
		String s = "RE: WO 29602: Comment - Submit a Request - Please make the following vip and dns changes on dev";
        String from = "santhosh.kuriakose@nbcuni.com";
		String status = postMessage(from,s,"Test again 123");
		System.out.println(status);
	}
		
	public static synchronized String postMessage(String from,String subject, String body){
		HttpURLConnection connection = null;
		DataInputStream input = null;
		
		/**Set up the environment variables for connecting to the Server*/
		System.getProperties().put("http.proxyHost", LHCommonConstants.getLh_http_proxy_host()); 
		System.getProperties().put("http.proxyPort", LHCommonConstants.getLh_http_proxy_port()); 
		System.getProperties().put("https.proxyHost", LHCommonConstants.getLh_https_proxy_host()); 
		System.getProperties().put("https.proxyPort", LHCommonConstants.getLh_https_proxy_port()); 
		//System.getProperties().put("http.proxyUser", "someUserName");
		//System.getProperties().put("http.proxyPassword", "somePassword"); 
		StringBuffer sb = new StringBuffer();
		String messageId = LHCommonUtils.getWorkOrderId(subject);
		String status = "ERR100";

		if (messageId.equals("-100")) return status;
		
		try {
			System.setProperty("java.protocol.handler.pkgs", "com.sun.net.ssl.internal.www.protocol");
			java.security.Security.addProvider(new com.sun.net.ssl.internal.ssl.Provider()); 
			String serviceURL = LHCommonConstants.getLh_comment_service_url();
			
			long currentTime = System.currentTimeMillis();
			String tokenInput = from+"|"+messageId+"|"+getHostName()+"|"+currentTime;
			
		    String data = URLEncoder.encode("lh_email", "UTF-8") + "=" + URLEncoder.encode(from, "UTF-8");
		    data += "&" + URLEncoder.encode("lh_wid", "UTF-8") + "=" + URLEncoder.encode(messageId, "UTF-8");
		    data += "&" + URLEncoder.encode("lh_token", "UTF-8") + "=" + URLEncoder.encode(LHCommentTokenGenerator.generateToken(tokenInput), "UTF-8");
		    data += "&" + URLEncoder.encode("lh_comment", "UTF-8") + "=" + URLEncoder.encode(body, "UTF-8");
		    data += "&" + URLEncoder.encode("source_host_name", "UTF-8") + "=" + URLEncoder.encode(getHostName(), "UTF-8");
		    data += "&" + URLEncoder.encode("lh_utc_time", "UTF-8") + "=" + URLEncoder.encode(""+currentTime, "UTF-8");
		    data += "&" + URLEncoder.encode("lh_subject", "UTF-8") + "=" + URLEncoder.encode(subject, "UTF-8");
		    data += "&" + URLEncoder.encode("lh_submit", "UTF-8") + "=" + URLEncoder.encode("Submit Query", "UTF-8");
		    
		    System.out.println(data);

		    URL myurl = new URL(serviceURL);
			
			if (serviceURL.toLowerCase().startsWith("https:")){
				connection = (HttpsURLConnection)myurl.openConnection(); 
			}else{
				connection = (HttpURLConnection)myurl.openConnection(); 
			}
			
			connection.setFollowRedirects(false); 
			connection.setRequestProperty("Content-length", String.valueOf(data.length()));  
			connection.setRequestProperty("Content-Type","application/x-www-form-urlencoded"); 
			//con.setRequestProperty("Content-Type","text/html"); 
			connection.setRequestProperty("User-Agent", "Mozilla/5.0 (Windows NT 5.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1");  
			connection.setDoOutput(true);  
			connection.setDoInput(true);  
			connection.setRequestMethod("POST");  
			
			DataOutputStream output = new DataOutputStream(connection.getOutputStream());     
			output.writeBytes(data);  
			output.close();  
			
			/**Gather the response from the service API*/
			sb = new StringBuffer();
			input = new DataInputStream( connection.getInputStream() );    
			
			for( int c = input.read(); c != -1; c = input.read() )
				sb.append((char)c );  
	
			System.out.println("Resp Code:"+connection .getResponseCode());  
			System.out.println("Resp Message:"+ connection .getResponseMessage()); 
			status = sb.toString();
		}
		catch (Exception e){
			e.printStackTrace();
		}
		finally {
			try {
				if (input!=null)input.close();
				if (connection!=null) connection.disconnect();
			}catch (Exception e){
				e.printStackTrace();
			}
		}
		return status;
	}

	private static String getHostName(){
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
