package com.nbcu.ots.lighthouse.comments.email;

import java.io.IOException;
import java.security.GeneralSecurityException;
import javax.crypto.Cipher;
import javax.crypto.SecretKey;
import javax.crypto.SecretKeyFactory;
import javax.crypto.spec.PBEKeySpec;
import javax.crypto.spec.PBEParameterSpec;
import org.apache.commons.codec.digest.DigestUtils;

import org.apache.commons.codec.digest.DigestUtils;

import sun.misc.BASE64Decoder;
import sun.misc.BASE64Encoder;

 public class LHTokenHandler {      
	 
	private static final char[] PASSWORD = "enfldsgbnlsngdlksdsgm".toCharArray();
	
	public static final String salt = "lighthouse";
	
    private static final byte[] SALT = { (byte) 0xde, (byte) 0x33, (byte) 0x10, (byte) 0x12,  (byte) 0xde, (byte) 0x33, (byte) 0x10, (byte) 0x12,};
     
    public static void main(String[] args) throws Exception {      
    	 
    	String originalPassword = "GE210User";
        System.out.println("Original password: " + originalPassword);
        String encryptedPassword = encrypt(originalPassword);
        System.out.println("Encrypted password: " + encryptedPassword);
        String decryptedPassword = decrypt(encryptedPassword);
        System.out.println("Decrypted password: " + decryptedPassword);
    }     
    
     static String encrypt(String property) throws GeneralSecurityException {         
    	SecretKeyFactory keyFactory = SecretKeyFactory.getInstance("PBEWithMD5AndDES");
        SecretKey key = keyFactory.generateSecret(new PBEKeySpec(PASSWORD));
        Cipher pbeCipher = Cipher.getInstance("PBEWithMD5AndDES");
        pbeCipher.init(Cipher.ENCRYPT_MODE, key, new PBEParameterSpec(SALT, 20));
        return base64Encode(pbeCipher.doFinal(property.getBytes()));
    }      
     static String base64Encode(byte[] bytes) {        
    	// NB: This class is internal, and you probably should use another impl         
    	return new BASE64Encoder().encode(bytes);
    }      
    
     static String decrypt(String property) throws GeneralSecurityException, IOException {         
    	SecretKeyFactory keyFactory = SecretKeyFactory.getInstance("PBEWithMD5AndDES");
        SecretKey key = keyFactory.generateSecret(new PBEKeySpec(PASSWORD));
        Cipher pbeCipher = Cipher.getInstance("PBEWithMD5AndDES");
        pbeCipher.init(Cipher.DECRYPT_MODE, key, new PBEParameterSpec(SALT, 20));
        return new String(pbeCipher.doFinal(base64Decode(property)));
    }    
     
      static byte[] base64Decode(String property) throws IOException {       
    	// NB: This class is internal, and you probably should use another impl         
    	return new BASE64Decoder().decodeBuffer(property);
    }  
      
	public static String generateToken(String input){
		
		return DigestUtils.md5Hex(input+"|"+salt);	
	}
 } 