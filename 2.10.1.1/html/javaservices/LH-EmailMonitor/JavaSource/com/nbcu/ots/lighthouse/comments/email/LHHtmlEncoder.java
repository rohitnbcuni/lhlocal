package com.nbcu.ots.lighthouse.comments.email;

import java.io.IOException;
import java.nio.charset.Charset;

public class LHHtmlEncoder {
	public static final LHHtmlEncoder INSTANCE = new LHHtmlEncoder();

	public static void encode(CharSequence sequence, Appendable out)
			throws IOException {

		for (int i = 0; i < sequence.length(); i++) {
			char ch = sequence.charAt(i);
			if (Character.UnicodeBlock.of(ch) == Character.UnicodeBlock.BASIC_LATIN) {
				out.append(ch);
			} else {
				int codepoint = Character.codePointAt(sequence, i);
				// handle supplementary range chars
				i += Character.charCount(codepoint) - 1;
				// emit entity
				out.append("&#x");
				out.append(Integer.toHexString(codepoint));
				out.append(";");
			}
		}
	}
	
	public static void main (String args[]) throws Exception {
		
		String s = "Weißt du";// Weiï¿½t du";
		StringBuffer sb = new StringBuffer();
		//encode(s,sb);
		
		byte [] b = s.getBytes("ISO-8859-1");
		System.out.println("b:"+new String (b));
		
		byte [] u = s.getBytes("UTF-8");
		System.out.println("u:"+new String (u));
		//System.out.println(sb.toString());
		
		String xml = "áéíóúñ"; 
		//byte[] latin1 = xml.getBytes("UTF-8"); 
		byte[] utf8 = new String(xml).getBytes("UTF-8");
		//System.out.println(new String(latin1));
		System.out.println(new String(utf8));
		System.out.println(new String(xml));
		
		System.out.println(Charset.defaultCharset());
		
		
		String contentType = "TEXT/PLAIN; charset=UTF-8";
		
	     String encoding = "";
        if (contentType!=null && contentType.indexOf("charset=")>-1){
      	  
      	  encoding = contentType.substring(contentType.indexOf("charset=")+8); 
      	  System.out.println(encoding);
        }
	}
}
