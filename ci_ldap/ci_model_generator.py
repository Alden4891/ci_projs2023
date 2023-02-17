import mysql.connector
db_connection = mysql.connector.connect(host="localhost",user="root",passwd="",database="db_cidatabase")

def get_tables():
	my_database = db_connection.cursor()
	sql_statement = "SHOW FULL TABLES WHERE TABLE_TYPE = 'BASE TABLE';"
	my_database.execute(sql_statement)
	output = my_database.fetchall()
	tables_list  = [x[0] for x in output]	
	return tables_list

def get_columns(table):
	my_database = db_connection.cursor()
	sql_statement = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '"+table+"';"
	my_database.execute(sql_statement)
	output = my_database.fetchall()
	tables_list  = [x[0] for x in output]	
	return tables_list

def generate_models(table):
	line = ""
	line += "\n<?php"
	line += "\nClass model_db_table_name extends CI_Model{"
	line += "\n"
	line += "\n   public function __construct(){"
	line += "\n      parent::__construct();"
	line += "\n      $this->load->database();"
	line += "\n   }"
	line += "\n   "
	line += "\n   public function read($index = -1){"
	line += "\n      if ($index == -1){"
	line += "\n         $query = $this->db->select('*')"
	line += "\n                           ->from('table_name')"
	line += "\n                           ->where(array('id' => $index))"
	line += "\n                           ->get();"
	line += "\n         return $query->result();"
	line += "\n    "
	line += "\n     }else{"
	line += "\n         $query = $this->db->select('*')"
	line += "\n                           ->from('table_name')"
	line += "\n                           ->where(array('id' => $index))"
	line += "\n                           ->get();"
	line += "\n         return $query->result();"
	line += "\n     }"
	line += "\n      "
	line += "\n  }"
	line += "\n"
	line += "\n   public function delete($index){"
	line += "\n       $this->db->delete('table_name', array('id' => $index));"
	line += "\n       return $this->db->affected_rows();"
	line += "\n   }"
	line += "\n   "
	line += "\n   public function delete_range($indexes){"
	line += "\n       $this->db->where_in('id', $indexes)->delete('table_name');"
	line += "\n       return $this->db->affected_rows();"
	line += "\n   }"
	line += "\n   "
	line += "\n   public function truncate($index){"
	line += "\n       $this->db->query('truncate table table_name;');"
	line += "\n       return $this->db->affected_rows();"
	line += "\n   }"
	line += "\n"
	line += "\n   public function insert($array_value)"
	line += "\n   {"
	line += "\n       $this->db->insert('table_name', $array_value);"
	line += "\n       return $this->db->affected_rows();"
	line += "\n   }"
	line += "\n"
	line += "\n   public function update($array_value,$array_condition)"
	line += "\n   {"
	line += "\n       $this->db->update('table_name', $array_value,$array_condition);"
	line += "\n       return $this->db->affected_rows();"
	line += "\n   }"
	line += "\n}"
	line = line.replace("table_name",table)
	print('./models/model_'+table+'.php -- success!')
	with open('./application/models/model_db_'+table+'.php', 'w') as f:
	    f.write(line)



for table in get_tables():
	generate_models(table)




