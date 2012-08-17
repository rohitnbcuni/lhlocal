package com.nbcu.ots.lighthouse.comments.email;

import java.util.Date;
import java.util.List;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import javax.mail.Address;
import javax.mail.Multipart;
import javax.mail.Part;
import javax.mail.internet.InternetAddress;
import javax.mail.internet.MimeBodyPart;

public class LHCommonUtils extends LHTokenHandler {

	private static final String SLASH_DATE_PATTERN = "^([1-9]|[0]\\d|[1][0-2])\\/(\\d|[0-2]\\d|[3][0-1])\\/([2][01]\\d{2}|[1][6-9]\\d{2})(\\s([0]\\d|\\d|[1][0-2])(\\:)([0-5]\\d){1,2})*\\s*([aApP][mM]{0,2})?$";
	private static final String HYPHEN_DATE_PATTERN = "^((31(?! (FEB|APR|JUN|SEP|NOV)))|((30|29)(?! FEB))|(29(?= FEB (((1[6-9]|[2-9]\\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00)))))|(0?[1-9])|1\\d|2[0-8])-(JAN|Jan|FEB|Feb|MAR|Mar|MAY|May|APR|Apr|JUL|Jul|JUN|Jun|AUG|Aug|OCT|Oct|SEP|Sep|Nov|NOV|Dec|DEC)-((1[6-9]|[2-9]\\d)\\d{2})(\\s([0]\\d|\\d|[1][0-2])(\\:)([0-5]\\d){1,2})*\\s*([aApP][mM]{0,2})?$";

	public static String getWorkOrderId(String subject) {
		String s = subject;
		try {
			s = s.substring(s.indexOf("WO ")).trim();
			s = s.substring(2, s.indexOf(":")).trim();

		} catch (Exception e) {
			//e.printStackTrace();
			s = "-100";
		}
		return s;
	}

	public static boolean isCritical(String subject) {
		if(subject !=null){
			subject = subject.toLowerCase();
			if (subject.startsWith("high:")
					|| subject.startsWith("high :")
					|| subject.startsWith("critical:")
					|| subject.startsWith("critical :")) {
				return true;
			}
		}
		return false;
	}

	public static String getSeverity(String subject) {
		String severity = "6";
		if(subject !=null){
			subject = subject.toLowerCase();
			if (subject.startsWith("low:")||subject.startsWith("low :")) {
				severity = "7";
			} else if (subject.startsWith("medium:") || subject.startsWith("medium :")) {
				severity = "6";
			} else if (subject.startsWith("high:") || subject.startsWith("high :")) {
				severity = "5";
			}
		}
		return severity;
	}

	public static String getWorkorderTitle(String subject) {
		String title = subject;
		if (subject.indexOf(":") != -1) {
			title = subject.substring(subject.indexOf(":") + 1, subject.length()).trim();
		}
		return title;

	}

	public static String parseWorkOrderDate(String date) {
		Pattern pattern;
		Matcher matcher;
		if (date.contains("/")) {
			pattern = Pattern.compile(SLASH_DATE_PATTERN);
			matcher = pattern.matcher(date);
			return validateDate(matcher, "MM/DD/YYYY");
		} else if (date.contains("-")) {
			pattern = Pattern.compile(HYPHEN_DATE_PATTERN);
			matcher = pattern.matcher(date);
			return validateDate(matcher, "DD-MON-YYYY");
		}
		return null;

	}

	public static void main(String[] args) {
		String date = "02/02/2012 05:00 PM";

		Date today = new Date();
		System.out.println(System.currentTimeMillis());
		// System.out.println(parseWorkOrderDate(date));
		// create a java calendar instance
		/*
		 * Calendar calendar = Calendar.getInstance();
		 * 
		 * // get a java.util.Date from the calendar instance. // this date will
		 * represent the current instant, or "now". java.util.Date now =
		 * calendar.getTime();
		 * 
		 * // a java current time (now) instance java.sql.Timestamp
		 * currentTimestamp = new java.sql.Timestamp(now.getTime());
		 * System.out.println("timestamp :"+currentTimestamp);
		 */
	}

	public static Part processMultiPartMsg(Part messagePart, List<Part> attachmentParts) throws Exception {
		Part tempMsgPart = null;

		if (messagePart.isMimeType("text/*")) {
			tempMsgPart = messagePart;
		} else if (messagePart.isMimeType("multipart/alternative")) {
			tempMsgPart = messagePart;
		} else if (messagePart.isMimeType("multipart/*")) {
			Multipart mPart = (Multipart) messagePart.getContent();
			int partCount = mPart.getCount();
			for (int i = 0; i < partCount; i++) {
				Part tempMsgPartRec = processMultiPartMsg(mPart.getBodyPart(i),
						attachmentParts);
				if (tempMsgPartRec != null) {
					tempMsgPart = tempMsgPartRec;
				}
			}
		} else {
			String disposition = messagePart.getDisposition();
			if ((disposition != null) && ((disposition.equals(Part.ATTACHMENT) || (disposition.equals(Part.INLINE))))) {
				if (disposition.equals(Part.INLINE)&& messagePart.isMimeType("image/*")) {
					if (messagePart.getSize() > LHCommonConstants.getLh_mail_signature_sizelimit()) {
						attachmentParts.add(messagePart);
					}
				} else {
					attachmentParts.add(messagePart);
				}
			} else if (disposition == null) {
				MimeBodyPart mbp = (MimeBodyPart) messagePart;
				if (mbp.isMimeType("image/*")) {
					if (messagePart.getSize() > LHCommonConstants.getLh_mail_signature_sizelimit()) {
						attachmentParts.add(messagePart);
					}
				}
			}
		}
		return tempMsgPart;
	}

	private static String validateDate(Matcher matcher, String format) {
		int year;
		String day;
		String month;
		String hour;
		String mins;
		String ampmVal;
		String returnDate = null;
		
		if (matcher.matches()) {
			matcher.reset();
			if (matcher.find()) {
				System.out.println("Matched");
				if (format == "MM/DD/YYYY") {
					month = matcher.group(1);
					day = matcher.group(2);
					year = Integer.parseInt(matcher.group(3));
					hour = matcher.group(5);
					mins = matcher.group(7);
					ampmVal = matcher.group(8);
				} else {
					month = matcher.group(14);
					String[] mon = { "JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "OCT", "SEP", "NOV", "DEC" };
					for (int i = 0; i < mon.length; i++) {
						if (month.equalsIgnoreCase(mon[i])) {
							month = Integer.toString(i+1);
							break;
						}
					}
					day = matcher.group(1);
					year = Integer.parseInt(matcher.group(15));
					hour = matcher.group(18);
					mins = matcher.group(20);
					ampmVal = matcher.group(21);
				}
				if(hour==null){
					hour = "18";// Set to EOD 6 PM
					mins = "00";
				}
				else{
					if(!"AM".equalsIgnoreCase(ampmVal)){
						hour = Integer.toString(Integer.parseInt(hour)+12);
					}
				}
				returnDate = year+"-"+String.format("%02d", Integer.parseInt(month))+"-"+String.format("%02d", Integer.parseInt(day))+" "+String.format("%02d", Integer.parseInt(hour))+":"+mins+":00";
				
				if (day.equals("31")
						&& (month.equals("4") || month.equals("6") || month.equals("9") || month.equals("11")
								|| month.equals("04") || month.equals("06") || month.equals("09"))) {
					return null; // only 1,3,5,7,8,10,12 has 31 days
				} else if (month.equals("2") || month.equals("02")) {
					// leap year
					if (year % 4 == 0) {
						if (year % 400 == 0) {
							if (day.equals("30") || day.equals("31")) {
								return null;
							} else {
								return returnDate;
							}
						}
					} else {
						if (day.equals("29") || day.equals("30") || day.equals("31")) {
							return null;
						} else {
							return returnDate;
						}
					}
				} else {
					return returnDate;
				}
			}
		}
		return null;
	}

	public static StringBuffer getWorkOrderCcList(Address[] addressArray, StringBuffer ccList) {
		if(addressArray!=null){
			for (int i = 0; i < addressArray.length; i++) {
				ccList.append(((InternetAddress) addressArray[i]).getAddress());
				if(i<(addressArray.length-1)){
					ccList.append(",");
				}
			}
		}
		return ccList;
	}
}
