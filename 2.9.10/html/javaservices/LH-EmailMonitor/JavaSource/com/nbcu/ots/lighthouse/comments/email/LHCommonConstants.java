package com.nbcu.ots.lighthouse.comments.email;

import java.io.FileInputStream;
import java.util.ArrayList;
import java.util.List;
import java.util.Properties;

public class LHCommonConstants extends LHTokenHandler{
	
	private static String lh_http_use_proxy = "";
	private static String lh_http_proxy_host = "";
	private static String lh_http_proxy_port = "";
	private static String lh_https_use_proxy = "";
	private static String lh_https_proxy_host = "";
	private static String lh_https_proxy_port = "";
	private static String lh_comment_service_url = "";
	private static String lh_comment_service_attachment_url = "";
	private static String lh_http_form_content_type = "";
	private static String lh_http_user_agent = "";
	private static String lh_http_method = "";
	private static String lh_mail_smtp_auth = "";
	private static String lh_mail_stmp_content_type = "";
	private static String lh_mail_smtp_starttls_enable = "";
	private static String lh_mail_smtp_host = "";
	private static String lh_mail_smtp_port = "";
	private static String lh_mail_smtp_user = "";
	private static String lh_mail_smtp_pwd = "";	
	private static String lh_mail_smtp_message_purge = "";	
	private static String lh_comment_service_success = "";
	private static String lh_mail_smtp_store_type = "smtp";
	private static String lh_comment_workdir = "";
	private static int lh_mail_signature_sizelimit = 134850; //Defaults to 100KB
	private static String lh_comment_service_workorder_url="";
	
	private static String lh_mail_smtp_commenting_host = "";
	private static String lh_mail_smtp_problem_user = "";
	private static String lh_mail_smtp_outage_user = "";
	private static String lh_mail_smtp_request_user = "";
	private static String lh_mail_smtp_problem_user_pwd = "";
	private static String lh_mail_smtp_outage_user_pwd = "";
	private static String lh_mail_smtp_request_user_pwd = "";
	
	private static List<String> lh_wo_email_list = new ArrayList<String>();
	
	public static String getLh_comment_service_workorder_url() {
		return lh_comment_service_workorder_url;
	}
	public static void setLh_comment_service_workorder_url(
			String lh_comment_service_workorder_url) {
		LHCommonConstants.lh_comment_service_workorder_url = lh_comment_service_workorder_url;
	}
	public static String getLh_comment_workdir() {
		return lh_comment_workdir;
	}
	public static void setLh_comment_workdir(String lh_comment_workdir) {
		LHCommonConstants.lh_comment_workdir = lh_comment_workdir;
	}
	public static String getLh_mail_smtp_store_type() {
		return lh_mail_smtp_store_type;
	}
	public static void setLh_mail_smtp_store_type(String lh_mail_smtp_store_type) {
		LHCommonConstants.lh_mail_smtp_store_type = lh_mail_smtp_store_type;
	}
	public static String getLh_comment_service_success() {
		return lh_comment_service_success;
	}
	public static void setLh_comment_service_success(
			String lh_comment_service_success) {
		LHCommonConstants.lh_comment_service_success = lh_comment_service_success;
	}
	public static String getLh_mail_smtp_message_purge() {
		return lh_mail_smtp_message_purge;
	}
	public static void setLh_mail_smtp_message_purge(
			String lh_mail_smtp_message_purge) {
		LHCommonConstants.lh_mail_smtp_message_purge = lh_mail_smtp_message_purge;
	}
	public static String getLh_http_use_proxy() {
		return lh_http_use_proxy;
	}
	public static void setLh_http_use_proxy(String lh_http_use_proxy) {
		LHCommonConstants.lh_http_use_proxy = lh_http_use_proxy;
	}
	public static String getLh_http_proxy_host() {
		return lh_http_proxy_host;
	}
	public static void setLh_http_proxy_host(String lh_http_proxy_host) {
		LHCommonConstants.lh_http_proxy_host = lh_http_proxy_host;
	}
	public static String getLh_http_proxy_port() {
		return lh_http_proxy_port;
	}
	public static void setLh_http_proxy_port(String lh_http_proxy_port) {
		LHCommonConstants.lh_http_proxy_port = lh_http_proxy_port;
	}
	
	public static String getLh_https_use_proxy() {
		return lh_https_use_proxy;
	}
	public static void setLh_https_use_proxy(String lh_https_use_proxy) {
		LHCommonConstants.lh_https_use_proxy = lh_https_use_proxy;
	}
	public static String getLh_https_proxy_host() {
		return lh_https_proxy_host;
	}
	public static void setLh_https_proxy_host(String lh_https_proxy_host) {
		LHCommonConstants.lh_https_proxy_host = lh_https_proxy_host;
	}
	public static String getLh_https_proxy_port() {
		return lh_https_proxy_port;
	}
	public static void setLh_https_proxy_port(String lh_https_proxy_port) {
		LHCommonConstants.lh_https_proxy_port = lh_https_proxy_port;
	}
	public static String getLh_comment_service_url() {
		return lh_comment_service_url;
	}
	public static void setLh_comment_service_url(String lh_comment_service_url) {
		LHCommonConstants.lh_comment_service_url = lh_comment_service_url;
	}
	public static String getLh_comment_service_attachment_url() {
		return lh_comment_service_attachment_url;
	}
	public static void setLh_comment_service_attachment_url(String lh_comment_service_attachment_url) {
		LHCommonConstants.lh_comment_service_attachment_url = lh_comment_service_attachment_url;
	}
	public static String getLh_http_form_content_type() {
		return lh_http_form_content_type;
	}
	public static void setLh_http_form_content_type(String lh_http_form_content_type) {
		LHCommonConstants.lh_http_form_content_type = lh_http_form_content_type;
	}
	public static String getLh_http_user_agent() {
		return lh_http_user_agent;
	}
	public static void setLh_http_user_agent(String lh_http_user_agent) {
		LHCommonConstants.lh_http_user_agent = lh_http_user_agent;
	}
	public static String getLh_http_method() {
		return lh_http_method;
	}
	public static void setLh_http_method(String lh_http_method) {
		LHCommonConstants.lh_http_method = lh_http_method;
	}
	public static String getLh_mail_smtp_auth() {
		return lh_mail_smtp_auth;
	}
	public static void setLh_mail_smtp_auth(String lh_mail_smtp_auth) {
		LHCommonConstants.lh_mail_smtp_auth = lh_mail_smtp_auth;
	}
	public static String getLh_mail_stmp_content_type() {
		return lh_mail_stmp_content_type;
	}
	public static void setLh_mail_stmp_content_type(String lh_mail_stmp_content_type) {
		LHCommonConstants.lh_mail_stmp_content_type = lh_mail_stmp_content_type;
	}
	public static String getLh_mail_smtp_starttls_enable() {
		return lh_mail_smtp_starttls_enable;
	}
	public static void setLh_mail_smtp_starttls_enable(
			String lh_mail_smtp_starttls_enable) {
		LHCommonConstants.lh_mail_smtp_starttls_enable = lh_mail_smtp_starttls_enable;
	}
	public static String getLh_mail_smtp_host() {
		return lh_mail_smtp_host;
	}
	public static void setLh_mail_smtp_host(String lh_mail_smtp_host) {
		LHCommonConstants.lh_mail_smtp_host = lh_mail_smtp_host;
	}
	public static String getLh_mail_smtp_port() {
		return lh_mail_smtp_port;
	}
	public static void setLh_mail_smtp_port(String lh_mail_smtp_port) {
		LHCommonConstants.lh_mail_smtp_port = lh_mail_smtp_port;
	}
	public static String getLh_mail_smtp_user() {
		return lh_mail_smtp_user;
	}
	public static void setLh_mail_smtp_user(String lh_mail_smtp_user) {
		LHCommonConstants.lh_mail_smtp_user = lh_mail_smtp_user;
	}
	public static String getLh_mail_smtp_pwd() {
		return lh_mail_smtp_pwd;
	}
	public static void setLh_mail_smtp_pwd(String lh_mail_smtp_pwd) throws Exception {
		LHCommonConstants.lh_mail_smtp_pwd = decrypt(lh_mail_smtp_pwd);
	}
	public static String getLh_mail_smtp_header_reply_to() {
		return lh_mail_smtp_header_reply_to;
	}
	public static void setLh_mail_smtp_header_reply_to(
			String lh_mail_smtp_header_reply_to) {
		LHCommonConstants.lh_mail_smtp_header_reply_to = lh_mail_smtp_header_reply_to;
	}
	public static int getLh_mail_signature_sizelimit() {
		return lh_mail_signature_sizelimit;
	}
	public static void setLh_mail_signature_sizelimit(
			int lh_mail_signature_sizelimit) {
		LHCommonConstants.lh_mail_signature_sizelimit = lh_mail_signature_sizelimit;
	}
	
	public static String getLh_mail_smtp_problem_user() {
		return lh_mail_smtp_problem_user;
	}
	public static void setLh_mail_smtp_problem_user(String lh_mail_smtp_problem_user) {
		LHCommonConstants.lh_mail_smtp_problem_user = lh_mail_smtp_problem_user;
	}
	public static String getLh_mail_smtp_outage_user() {
		return lh_mail_smtp_outage_user;
	}
	public static void setLh_mail_smtp_outage_user(String lh_mail_smtp_outage_user) {
		LHCommonConstants.lh_mail_smtp_outage_user = lh_mail_smtp_outage_user;
	}
	public static String getLh_mail_smtp_request_user() {
		return lh_mail_smtp_request_user;
	}
	public static void setLh_mail_smtp_request_user(String lh_mail_smtp_request_user) {
		LHCommonConstants.lh_mail_smtp_request_user = lh_mail_smtp_request_user;
	}


	public static String getLh_mail_smtp_problem_user_pwd() {
		return lh_mail_smtp_problem_user_pwd;
	}
	public static void setLh_mail_smtp_problem_user_pwd(String lh_mail_smtp_problem_user_pwd) throws Exception {
		LHCommonConstants.lh_mail_smtp_problem_user_pwd = decrypt(lh_mail_smtp_problem_user_pwd);
	}
	public static String getLh_mail_smtp_outage_user_pwd() {
		return lh_mail_smtp_outage_user_pwd;
	}
	public static void setLh_mail_smtp_outage_user_pwd(String lh_mail_smtp_outage_user_pwd) throws Exception {
		LHCommonConstants.lh_mail_smtp_outage_user_pwd = decrypt(lh_mail_smtp_outage_user_pwd);
	}
	public static String getLh_mail_smtp_request_user_pwd() {
		return lh_mail_smtp_request_user_pwd;
	}
	public static void setLh_mail_smtp_request_user_pwd(String lh_mail_smtp_request_user_pwd) throws Exception {
		LHCommonConstants.lh_mail_smtp_request_user_pwd = decrypt(lh_mail_smtp_request_user_pwd);
	}

	public static List<String> getLh_wo_email_list() {
		return lh_wo_email_list;
	}
	public static void setLh_wo_email_list(List<String> lh_wo_email_list) {
		LHCommonConstants.lh_wo_email_list = lh_wo_email_list;
	}
	public static void addLh_wo_email_list(String lh_wo_email_n_pass) {
		lh_wo_email_list.add(lh_wo_email_n_pass);
	}

	public static String getLh_mail_smtp_commenting_host() {
		return lh_mail_smtp_commenting_host;
	}
	public static void setLh_mail_smtp_commenting_host(String lh_mail_smtp_commenting_host) {
		LHCommonConstants.lh_mail_smtp_commenting_host = lh_mail_smtp_commenting_host;
	}



	private static String lh_mail_smtp_header_reply_to = "";
	
	public static synchronized void init(String fileName) {

		Properties properties = new Properties();
		try {
			properties.load(new FileInputStream(fileName));

			setLh_http_use_proxy(properties.getProperty("lh.http.use.proxy"));
			setLh_http_proxy_host(properties.getProperty("lh.http.proxy.host"));
			setLh_http_proxy_port(properties.getProperty("lh.http.proxy.port"));
			setLh_https_use_proxy(properties.getProperty("lh.https.use.proxy"));
			setLh_https_proxy_host(properties.getProperty("lh.https.proxy.host"));
			setLh_https_proxy_port(properties.getProperty("lh.https.proxy.port"));
			setLh_comment_service_url(properties.getProperty("lh.comment.service.url"));
			setLh_comment_service_attachment_url(properties.getProperty("lh.comment.service.attachment.url"));
			setLh_http_form_content_type(properties.getProperty("lh.http.form.content.type"));
			setLh_http_user_agent(properties.getProperty("lh.http.user.agent"));
			setLh_http_method(properties.getProperty("lh.http.method"));
			setLh_mail_smtp_auth(properties.getProperty("lh.mail.smtp.auth"));
			setLh_mail_stmp_content_type(properties.getProperty("lh.mail.stmp.content.type"));
			setLh_mail_smtp_starttls_enable(properties.getProperty("lh.mail.smtp.starttls.enable"));
			setLh_mail_smtp_host(properties.getProperty("lh.mail.smtp.host"));
			setLh_mail_smtp_port(properties.getProperty("lh.mail.smtp.port"));
			setLh_mail_smtp_user(properties.getProperty("lh.mail.smtp.user"));
			setLh_mail_smtp_pwd(properties.getProperty("lh.mail.smtp.pwd"));
			setLh_mail_smtp_message_purge(properties.getProperty("lh.mail.smtp.message.purge"));
			setLh_comment_service_success(properties.getProperty("lh.comment.service.success"));
			setLh_mail_smtp_store_type(properties.getProperty("lh.mail.smtp.store.type"));
			setLh_comment_workdir(properties.getProperty("lh.comment.workdir"));
			setLh_mail_signature_sizelimit(Integer.parseInt(properties.getProperty("lh.mail.signature.sizelimit")));
			setLh_comment_service_workorder_url(properties.getProperty("lh.comment.service.workorder.url"));

			setLh_mail_smtp_commenting_host(properties.getProperty("lh.mail.smtp.commenting.host"));
			setLh_mail_smtp_problem_user(properties.getProperty("lh.mail.smtp.problem.user"));
			setLh_mail_smtp_outage_user(properties.getProperty("lh.mail.smtp.outage.user"));
			setLh_mail_smtp_request_user(properties.getProperty("lh.mail.smtp.request.user"));
			setLh_mail_smtp_problem_user_pwd(properties.getProperty("lh.mail.smtp.problem.pwd"));
			setLh_mail_smtp_outage_user_pwd(properties.getProperty("lh.mail.smtp.outage.pwd"));
			setLh_mail_smtp_request_user_pwd(properties.getProperty("lh.mail.smtp.request.pwd"));
			
			addLh_wo_email_list("PROBLEM:"+properties.getProperty("lh.mail.smtp.problem.email")+":"+getLh_mail_smtp_problem_user()+":"+getLh_mail_smtp_problem_user_pwd());
			addLh_wo_email_list("OUTAGE:"+properties.getProperty("lh.mail.smtp.outage.email")+":"+getLh_mail_smtp_outage_user()+":"+getLh_mail_smtp_outage_user_pwd());
			addLh_wo_email_list("REQUEST:"+properties.getProperty("lh.mail.smtp.request.email")+":"+getLh_mail_smtp_request_user()+":"+getLh_mail_smtp_request_user_pwd());

		} catch (Exception e) {
			e.printStackTrace();
		}
	}
	
	public static void main (String[] args){
		
		init("C:\\Jsch\\lh-comment-mail.properties");
		
        System.out.println("lh_http_use_proxy: "+ lh_http_use_proxy );
    	System.out.println("lh_http_proxy_host: "+ lh_http_proxy_host );
    	System.out.println("lh_http_proxy_port: "+ lh_http_proxy_port );
        System.out.println("lh_http_use_proxy: "+ lh_https_use_proxy );
    	System.out.println("lh_http_proxy_host: "+ lh_https_proxy_host );
    	System.out.println("lh_http_proxy_port: "+ lh_https_proxy_port );
    	System.out.println("lh_comment_service_url: "+ lh_comment_service_url );
    	System.out.println("lh_http_form_content_type: "+ lh_http_form_content_type );
    	System.out.println("lh_http_user_agent: "+ lh_http_user_agent );
    	System.out.println("lh_http_method: "+ lh_http_method );
    	System.out.println("lh_mail_smtp_auth: "+ lh_mail_smtp_auth );
    	System.out.println("lh_mail_stmp_content_type: "+ lh_mail_stmp_content_type );
    	System.out.println("lh_mail_smtp_starttls_enable: "+ lh_mail_smtp_starttls_enable );
    	System.out.println("lh_mail_smtp_host: "+ lh_mail_smtp_host );
    	System.out.println("lh_mail_smtp_port: "+ lh_mail_smtp_port );
    	System.out.println("lh_mail_smtp_user: "+ lh_mail_smtp_user );
    	System.out.println("lh_mail_smtp_pwd: "+ lh_mail_smtp_pwd );	
    	System.out.println("lh_mail_smtp_message_purge: "+ lh_mail_smtp_message_purge );
    	System.out.println("lh_comment_service_success: "+ lh_comment_service_success );
    	System.out.println("lh_mail_smtp_store_type: "+ lh_mail_smtp_store_type );
	}

}
