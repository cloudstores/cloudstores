package apiServerPackage;

import java.io.IOException;

import javax.servlet.ServletException;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import org.eclipse.jetty.continuation.Continuation;

import apiServerPackage.APIServer.RequestTypes;

public interface RequestHandlerInterface {
	
	public void handleGenericRequest (HttpServletRequest request, HttpServletResponse response, Continuation continuation,
			RequestTypes reqType)  throws ServletException, IOException;
	
	public void handlePost(HttpServletRequest request, HttpServletResponse response,Continuation continuation) throws ServletException, IOException;
	
	public void handleGet(HttpServletRequest request, HttpServletResponse response,Continuation continuation) throws ServletException, IOException;
	
	public void handlePut(HttpServletRequest request, HttpServletResponse response,Continuation continuation) throws ServletException, IOException;
	
	public void handleDelete(HttpServletRequest request, HttpServletResponse response,Continuation continuation) throws ServletException, IOException;
	
	public void sendFailedResponse(HttpServletResponse response,Continuation continuation);
}
