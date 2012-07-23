package com.nbcu.ots.lighthouse.comments.email;

import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.ObjectInputStream;
import java.io.ObjectOutputStream;
import java.util.HashMap;
import java.util.Iterator;
import java.util.Map;
import java.util.Set;

public class LHEmailMetadataSerializer implements java.io.Serializable {

	public static HashMap metadataList = new HashMap();
	public static String fileName = "/session/lhemailmetadata.ser";

	/**
	 * @param args
	 */
	public static void main(String[] args) {
		// TODO Auto-generated method stub

		deserialize();
		addMessage("ONE");
		print();
		addMessage("TWO");
		print();

		removeMessage("ONE");

		print();
		serialize();
		// print();

	}

	public static synchronized void addMessage(String messageId) {

		if (messageId==null) return;
		
		if (metadataList.containsKey(messageId)) {

			Integer currentValue = (Integer) metadataList.get(messageId);
			Integer newValue = new Integer(currentValue.intValue() + 1);
			metadataList.put(messageId, newValue);
		} else {
			metadataList.put(messageId, new Integer(1));
		}
	}

	public static synchronized void removeMessage(String messageId) {

		if (messageId!=null && metadataList.containsKey(messageId)) {

			metadataList.remove(messageId);
		}
	}

	public static int getMessageCount(String messageId) {

		int count = 0;

		if (messageId!=null && metadataList.containsKey(messageId)) {

			Integer currentValue = (Integer) metadataList.get(messageId);
			count = currentValue.intValue();

		}

		return count;
	}

	public static boolean isMessageExist(String messageId) {

		boolean messageExists = false;
		Set set = metadataList.entrySet();

		// Get an iterator
		Iterator i = set.iterator();
		// Display elements
		while (i.hasNext()) {
			Map.Entry me = (Map.Entry) i.next();
			Object key = me.getKey();

			if (messageId.equals(key)) {
				messageExists = true;
				break;
			}
			// Object value = me.getValue();
			// System.out.print(key + ": ");
			// System.out.println(value);
		}

		return messageExists;

	}

	public static void print() {

		Set set = metadataList.entrySet();
		// Get an iterator
		Iterator i = set.iterator();
		// Display elements
		while (i.hasNext()) {
			Map.Entry me = (Map.Entry) i.next();
			System.out.print(me.getKey() + ": ");
			System.out.println(me.getValue());

		}
	}

	public static void serialize() {

		FileOutputStream fos = null;
		ObjectOutputStream oos = null;
		try {

			//FileOutputStream fos = new FileOutputStream("C:\\Jsch\\lhemailmetadata.ser");
			 fos = new FileOutputStream(LHCommonConstants.getLh_comment_workdir()+fileName);
			 oos = new ObjectOutputStream(fos);
			oos.writeObject(metadataList);
			oos.flush();
			
		} catch (Exception e) {
			System.out.println("Exception during serialization: " + e);
			// System.exit(0);
		}finally{
			
			try {
				
				if (oos!=null)oos.close();
				if (fos!=null)fos.close();
				
			}catch (Exception e){
				
				e.printStackTrace();
			}
			
		}

	}

	public static void deserialize() {

		FileInputStream fis = null;
		ObjectInputStream ois = null;
		try {

			 fis = new FileInputStream(LHCommonConstants.getLh_comment_workdir()+fileName);
			 ois = new ObjectInputStream(fis);
			metadataList = (HashMap) ois.readObject();

		} catch (FileNotFoundException fnfe) {
			System.out.println("Exception during deserialization: " + fnfe);
			serialize();
			//metadataList = new HashMap();

		}catch(IOException ioe){
			
			ioe.printStackTrace();
			
     	}catch(Exception e){
     		
     		e.printStackTrace();
     	}
	 finally{
			
			try {
				
				if (ois!=null)ois.close();
				if (fis!=null)fis.close();
				
			}catch (Exception e){
				
				e.printStackTrace();
			}
			
		}
	}

}
