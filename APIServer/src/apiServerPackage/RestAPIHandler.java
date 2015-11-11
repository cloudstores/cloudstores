//package apiServerPackage;
package apiServerPackage;

import java.io.IOException;
import java.net.InetSocketAddress;

import javax.servlet.Servlet;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import org.eclipse.jetty.server.Request;
import org.eclipse.jetty.server.Server;
import org.eclipse.jetty.server.handler.AbstractHandler;
import org.eclipse.jetty.servlet.ServletContextHandler;
import org.eclipse.jetty.servlet.ServletHolder;
import org.apache.log4j.Logger;
import org.apache.log4j.BasicConfigurator;
import org.apache.log4j.PropertyConfigurator;

import com.datastax.driver.core.Cluster;
import com.datastax.driver.core.Session;

class APIServer{
	static private int bindPort;
	static private String bindIpaddr;
	private Server server;
	String logPrefix="APIServer: ";
	final Logger logger = Logger.getLogger(APIServer.class);
	private ServletContextHandler context;
	
	enum RequestTypes{
		POST,
		PUT,
		GET,
		DELETE,
	}
	
	APIServer(String ipAdd, String port){
		bindPort = Integer.parseInt(port);
		bindIpaddr = ipAdd;
		this.server = new Server(new InetSocketAddress(ipAdd,Integer.parseInt(port)));
	}
	
	public void start() {
		try {
			this.server.start();
			logger.info(logPrefix+" API server started on port:"+bindPort);
		} catch (Exception e) {
			e.printStackTrace();
		}
	}
	
	public void join() {
		try {
			this.server.join();
			logger.info(logPrefix+" API server stopped");
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
	}
	
	public void stop(){
		try {
			this.server.stop();
			logger.info(logPrefix+" API server stopped");
		} catch (Exception e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
	}
	
	public Server getServer(){
		return this.server;
	}
	
	public void setContextHandler(){
		this.context = new ServletContextHandler(ServletContextHandler.SESSIONS);
		this.context.setContextPath("/restapihandler");
		this.server.setHandler(this.context);
	}
	
	public void addServletIntoContext(){
		//this.context.addServlet(new ServletHolder(), "/login/*");
		this.context.addServlet(new ServletHolder(new RegisterUser()), "/register/*");
	}

}


/*class HelloHandler extends AbstractHandler
{
	final Logger logger = Logger.getLogger(HelloHandler.class);
	String logPrefix="HelloHandler:";
    public void handle(String target,Request baseRequest,HttpServletRequest request,HttpServletResponse response) 
        throws IOException, ServletException
    {
        response.setContentType("text/html;charset=utf-8");
        response.setStatus(HttpServletResponse.SC_OK);
        baseRequest.setHandled(true);
        response.getWriter().println("<h1>Hello World</h1>");
        System.out.println(logPrefix+" handler response:"+HttpServletResponse.SC_OK);
        logger.info(logPrefix+" handler response:"+HttpServletResponse.SC_OK);
    }
}*/


public class RestAPIHandler {
	public static final Logger logger = Logger.getLogger(RestAPIHandler.class);
	public static void main(String []arg){
		BasicConfigurator.configure();
		APIServer apiServer = new APIServer("127.0.0.1","8080");
		apiServer.setContextHandler();
		apiServer.addServletIntoContext();
		apiServer.start();
		apiServer.join();
		}
}
