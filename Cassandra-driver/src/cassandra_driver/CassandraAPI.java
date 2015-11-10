package cassandra_driver;

import com.datastax.driver.core.Cluster;
import com.datastax.driver.core.ResultSet;
import com.datastax.driver.core.Row;
import com.datastax.driver.core.Session;

public class CassandraAPI {
	private static Cluster cluster;
	private static Session session;
	
	CassandraAPI(){
		
	}
	
	CassandraAPI(String keyspace){
		
	}
	
	public void read(){
		
	}
	
	public static void main(String []arg){
		cluster = Cluster.builder().addContactPoint("127.0.0.1").build();
		session = cluster.connect("configDbKeyspace");
		System.out.println("CassandraAPI.CassandraAPI()");
		ResultSet results = session.execute("SELECT * FROM system.schema_keyspaces");
		for (Row row : results) {
			System.out.println(row.getString("keyspace_name"));
		}
		session.close();
		cluster.close();
	}
	
}
