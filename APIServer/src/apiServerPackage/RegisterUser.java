package apiServerPackage;

import java.io.BufferedReader;
import java.io.IOException;

import javax.servlet.Servlet;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import org.apache.log4j.Logger;
import org.eclipse.jetty.continuation.Continuation;
import org.eclipse.jetty.continuation.ContinuationSupport;
import org.json.simple.JSONObject;
import org.json.simple.JSONValue;

import apiServerPackage.APIServer.RequestTypes;

class Register{
	String firstName;
	String lastName;
	String emailId;
	String phoneNo;
	String DOB;
	public String post;
}

public class RegisterUser extends HttpServlet implements RequestHandlerInterface {
	public static final Logger logger = Logger.getLogger(RestAPIHandler.class);
	String logPrefix="RegisterUser: ";
	static private Continuation continuation;
	
	
	
	
	public RegisterUser(){
		super();
	}
	
	//http://localhost:8080/restapihandler/register/
	protected void doPost(HttpServletRequest request, HttpServletResponse response) 
			throws IOException, ServletException{
		
		String request_uri = request.getRequestURI();

		if(null == request_uri){
			logger.error(logPrefix+"No URI found in the request: "+request);
			String msg="No URI found in the request";
			sendResponse(response, HttpServletResponse.SC_BAD_REQUEST, msg);
			return;
		}
		logger.debug(logPrefix+"Requested URI is: " + request_uri);
		continuation= ContinuationSupport.getContinuation(request); 
		continuation.suspend(response);
		
		handleGenericRequest(request, response, continuation, RequestTypes.POST);
	}
	
	@Override
	public void handleGenericRequest(HttpServletRequest request,
			HttpServletResponse response, Continuation continuation,
			RequestTypes reqType) throws ServletException, IOException {
		// TODO Auto-generated method stub
		StringBuffer sb = new StringBuffer();
		String line = null;
        try {
        	BufferedReader reader = request.getReader();
            while ((line = reader.readLine()) != null)
            	sb.append(line);
        }catch (Exception e) {
        	logger.error(logPrefix+"Exception is: "+e);
        	String message = "Exception while reading request";
        	sendResponse(response, response.SC_BAD_REQUEST, message);
        	return;
        }
        JSONObject jsonObject;
        jsonObject = (JSONObject) JSONValue.parse(sb.toString());
        if(null == jsonObject)
        {
        	    logger.error(logPrefix+"Failed in parsing Registration details");
        	    String message = "Failed in parsing Registration details";
        	    sendResponse(response, response.SC_BAD_REQUEST, message);
                return;
        }
        Register reg = new Register();
        reg.firstName = (String) jsonObject.get("firstName");
        reg.lastName    = (String) jsonObject.get("lastName");
        reg.DOB = (String) jsonObject.get("dob");
        reg.emailId = (String) jsonObject.get("emailId");
        reg.phoneNo = (String) jsonObject.get("phoneNo");
        reg.post = (String) jsonObject.get("post");
        logger.info(logPrefix+" first Name:"+reg.firstName+" last Name:"+
        		reg.lastName+" DOB:"+reg.DOB+" emailId:"+reg.emailId+" Post:"+reg.post);
        
        sendResponse(response,HttpServletResponse.SC_ACCEPTED,"Registration success");
	}
	
	private void sendResponse(HttpServletResponse response, int HttpServletStatus, String message) throws IOException {
		response.setContentType("text/html;charset=utf-8");
        response.setStatus(HttpServletStatus);
        response.getWriter().println(message);
        logger.info(logPrefix+" handler response:"+HttpServletStatus);
        continuation.complete();
        
	}

	@Override
	public void handlePost(HttpServletRequest request,
			HttpServletResponse response, Continuation continuation)
			throws ServletException, IOException {
		// TODO Auto-generated method stub

	}

	@Override
	public void handleGet(HttpServletRequest request,
			HttpServletResponse response, Continuation continuation)
			throws ServletException, IOException {
		// TODO Auto-generated method stub

	}

	@Override
	public void handlePut(HttpServletRequest request,
			HttpServletResponse response, Continuation continuation)
			throws ServletException, IOException {
		// TODO Auto-generated method stub

	}

	@Override
	public void handleDelete(HttpServletRequest request,
			HttpServletResponse response, Continuation continuation)
			throws ServletException, IOException {
		// TODO Auto-generated method stub

	}

	@Override
	public void sendFailedResponse(HttpServletResponse response,
			Continuation continuation) {
		// TODO Auto-generated method stub

	}

}
