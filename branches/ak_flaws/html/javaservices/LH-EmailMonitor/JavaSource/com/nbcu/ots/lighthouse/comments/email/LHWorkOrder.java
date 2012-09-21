package com.nbcu.ots.lighthouse.comments.email;

import java.util.List;

import javax.mail.Part;

public class LHWorkOrder {
	
	private String emailId;
	private String type;
	private String title;
	private String description;
	private String ccList;
	private List<Part> attachments;
	private String severity;
	private boolean criticality;
	private String project;
	private String site;
	private String url;
	private String date;
	
	public String getEmailId() {
		return emailId;
	}
	public void setEmailId(String emailId) {
		this.emailId = emailId;
	}
	public String getType() {
		return type;
	}
	public void setType(String type) {
		this.type = type;
	}
	public String getTitle() {
		return title;
	}
	public void setTitle(String title) {
		this.title = title;
	}
	public String getDescription() {
		return description;
	}
	public void setDescription(String description) {
		this.description = description;
	}
	public String getCcList() {
		return ccList;
	}
	public void setCcList(String ccList) {
		this.ccList = ccList;
	}
	public List<Part> getAttachments() {
		return attachments;
	}
	public void setAttachments(List<Part> attachments) {
		this.attachments = attachments;
	}
	public String getSeverity() {
		return severity;
	}
	public void setSeverity(String severity) {
		this.severity = severity;
	}
	public boolean getCriticality() {
		return criticality;
	}
	public void setCriticality(boolean criticality) {
		this.criticality = criticality;
	}
	public String getProject() {
		return project;
	}
	public void setProject(String project) {
		this.project = project;
	}
	public String getSite() {
		return site;
	}
	public void setSite(String site) {
		this.site = site;
	}
	public String getUrl() {
		return url;
	}
	public void setUrl(String url) {
		this.url = url;
	}
	public String getDate() {
		return date;
	}
	public void setDate(String date) {
		this.date = date;
	}
	

}
