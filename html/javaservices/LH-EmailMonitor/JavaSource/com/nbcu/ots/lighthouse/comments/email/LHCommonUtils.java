package com.nbcu.ots.lighthouse.comments.email;


public class LHCommonUtils extends LHTokenHandler{
	
	public static String getWorkOrderId(String subject){
		String s = subject;
		try {
			s = s.substring(s.indexOf("WO ")).trim();
			s = s.substring(2,s.indexOf(":")).trim();
			
		}catch (Exception e){
			e.printStackTrace();
			s = "-100";
		}		
		return s;
	}

}
