package com.nbcu.ots.lighthouse.comments.email;

import java.text.SimpleDateFormat;
import java.util.Date;

public class LHCommentFilter {

	private static String OutlookMailDelimiter = "________________________________\n";
	private static String blackBerryDelimiter = "From: ";
	
	/**
	 * @param args
	 */
	public static void main(String[] args) {
		// TODO Auto-generated method stub
		
		String iPhonestart = "afadfd On Apr 6, 2012, at 11:21 AM,  <Derek.Yovine@nbcuni.com> wrote:";
		
		String iPhoneDel = getiPhoneDelimiter();
		
	System.out.println(iPhonestart.indexOf(iPhoneDel));
		

     String comment = "start________________________________\nyour";
		
		String fComment = filterComment(iPhonestart);
		
		System.out.println(fComment);
	
	}
	
public static String filterComment(String comment){
	
	String lComment = comment ;
	if (lComment == null) return "";
	
	int index = lComment.indexOf(OutlookMailDelimiter);
	
	if (index > -1){
		
		lComment = lComment.substring(0, index);
	}
	
	int index1 = lComment.indexOf(getiPhoneDelimiter());
	
	if (index1 > -1){
		
		lComment = lComment.substring(0, index1);
	}
	
	int index2 = lComment.indexOf(blackBerryDelimiter);
	
	if (index2 > -1){
		
		lComment = lComment.substring(0, index2);
	}
	
	return lComment;
}
		
public static String getiPhoneDelimiter(){
	
	
	// Create Date object.
	 Date date = new Date();
	 //Specify the desired date format
	 String DATE_FORMAT = "MMM d, yyyy";
	 //Create object of SimpleDateFormat and pass the desired date format.
	 SimpleDateFormat sdf = new SimpleDateFormat(DATE_FORMAT);
	 
	 /*
	 Use format method of SimpleDateFormat class to format the date.
	 */
	 //System.out.println("Today is " + sdf.format(date) );
   
	 return "On "+sdf.format(date)+", at ";
}
	
}
