package com.nbcu.ots.lighthouse.comments.email;
import org.apache.commons.codec.digest.DigestUtils;
public class LHCommentTokenGenerator {
	
	public static final String salt = "lighthouse";

	/**
	 * @param args
	 */
	public static void main(String[] args) {
		// TODO Auto-generated method stub

	System.out.println(DigestUtils.md5Hex("ShobhitSingh.Bhadauria@nbcuni.com|27738|useclwslp033.nbcuni.ge.com|1333638181878|lighthouse"));
    System.out.println(System.currentTimeMillis()) ;
	
	}
	
	public static String generateToken(String input){
		
		return DigestUtils.md5Hex(input+"|"+salt);	
	}

}
