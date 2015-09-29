<?php
 	require_once("Rest.inc.php");
	
	class API extends REST {
	
		public $data = "";
		
		const DB_SERVER = "localhost";
		const DB_USER = "root";
		const DB_PASSWORD = "";
		const DB = "angularjs";

		private $db = NULL;
		private $mysqli = NULL;
		public function __construct(){
			parent::__construct();				// Init parent contructor
			$this->dbConnect();					// Initiate Database connection
		}
		
		/*
		 *  Connect to Database
		*/
		private function dbConnect(){
			$this->mysqli = new mysqli(self::DB_SERVER, self::DB_USER, self::DB_PASSWORD, self::DB);
		}
		
		/*
		 * Dynmically call the method based on the query string
		 */
		public function processApi(){
			$func = strtolower(trim(str_replace("/","",$_REQUEST['x'])));
			if((int)method_exists($this,$func) > 0)
				$this->$func();
			else
				$this->response('',404); // If the method not exist with in this class "Page not found".
		}
				
		private function login(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$email = $this->_request['email'];		
			$password = $this->_request['pwd'];
			if(!empty($email) and !empty($password)){
				if(filter_var($email, FILTER_VALIDATE_EMAIL)){
					$query="SELECT uid, name, email FROM users WHERE email = '$email' AND password = '".md5($password)."' LIMIT 1";
					$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

					if($r->num_rows > 0) {
						$result = $r->fetch_assoc();	
						// If success everythig is good send header as "OK" and user details
						$this->response($this->json($result), 200);
					}
					$this->response('', 204);	// If no records "No Content" status
				}
			}
			
			$error = array('status' => "Failed", "msg" => "Invalid Email address or Password");
			$this->response($this->json($error), 400);
		}
		
		private function students(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$query="SELECT distinct s.studentNumber, s.studentName, s.email, s.address, s.city, s.state, s.postalCode, s.country FROM students s order by s.studentNumber desc";
			$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

			if($r->num_rows > 0){
				$result = array();
				while($row = $r->fetch_assoc()){
					$result[] = $row;
				}
				$this->response($this->json($result), 200); // send user details
			}
			$this->response('',204);	// If no records "No Content" status
		}
		
		
		private function student(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$id = (int)$this->_request['id'];
			if($id > 0){	
				$query="SELECT distinct s.studentNumber, s.studentName, s.email, s.address, s.city, s.state, s.postalCode, s.country FROM students s where s.studentNumber=$id";
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				if($r->num_rows > 0) {
					$result = $r->fetch_assoc();	
					$this->response($this->json($result), 200); // send user details
				}
			}
			$this->response('',204);	// If no records "No Content" status
		}
		
		
		private function insertStudent(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}

			$student = json_decode(file_get_contents("php://input"),true);
			$column_names = array('studentName', 'email', 'city', 'address', 'country');
			$keys = array_keys($student);
			$columns = '';
			$values = '';
			foreach($column_names as $desired_key){ // Check the customer received. If blank insert blank into the array.
			   if(!in_array($desired_key, $keys)) {
			   		$$desired_key = '';
				}else{
					$$desired_key = $student[$desired_key];
				}
				$columns = $columns.$desired_key.',';
				$values = $values."'".$$desired_key."',";
			}
			$query = "INSERT INTO students(".trim($columns,',').") VALUES(".trim($values,',').")";
			if(!empty($student)){
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				$success = array('status' => "Success", "msg" => "Student Created Successfully.", "data" => $student);
				$this->response($this->json($success),200);
			}else
				$this->response('',204);	//"No Content" status
		}
		
		private function updateStudent(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$student = json_decode(file_get_contents("php://input"),true);
			$id = (int)$student['id'];
			$column_names = array('studentName', 'email', 'city', 'address', 'country');
			$keys = array_keys($student['student']);
			$columns = '';
			$values = '';
			foreach($column_names as $desired_key){ // Check the customer received. If key does not exist, insert blank into the array.
			   if(!in_array($desired_key, $keys)) {
			   		$$desired_key = '';
				}else{
					$$desired_key = $student['student'][$desired_key];
				}
				$columns = $columns.$desired_key."='".$$desired_key."',";
			}
			$query = "UPDATE students SET ".trim($columns,',')." WHERE studentNumber=$id";
			if(!empty($student)){
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				$success = array('status' => "Success", "msg" => "Student ".$id." Updated Successfully.", "data" => $student);
				$this->response($this->json($success),200);
			}else
				$this->response('',204);	// "No Content" status
		}
		
		
		private function deleteStudent(){
			if($this->get_request_method() != "DELETE"){
				$this->response('',406);
			}
			$id = (int)$this->_request['id'];
			if($id > 0){				
				$query="DELETE FROM students WHERE studentNumber = $id";
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				$success = array('status' => "Success", "msg" => "Successfully deleted one record.");
				$this->response($this->json($success),200);
			}else
				$this->response('',204);	// If no records "No Content" status
		}
	
//Tag  api calls

		private function tags(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$query="SELECT distinct t.tag_id, t.tag_name FROM tags t order by t.tag_id desc";
			$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

			if($r->num_rows > 0){
				$result = array();
				while($row = $r->fetch_assoc()){
					$result[] = $row;
				}
				$this->response($this->json($result), 200); // send user details
			}
			$this->response('',204);	// If no records "No Content" status
		}
		
		private function tag(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$id = (int)$this->_request['id'];
			if($id > 0){	
				$query="SELECT distinct t.tag_id, t.tag_name FROM tags t where t.tag_id=$id";
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				if($r->num_rows > 0) {
					$result = $r->fetch_assoc();	
					$this->response($this->json($result), 200); // send user details
				}
			}
			$this->response('',204);	// If no records "No Content" status
		}
		
		private function insertTag(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}

			$tag = json_decode(file_get_contents("php://input"),true);
			$column_names = array('tag_name');
			$keys = array_keys($tag);
			$columns = '';
			$values = '';
			foreach($column_names as $desired_key){ // Check the customer received. If blank insert blank into the array.
			   if(!in_array($desired_key, $keys)) {
			   		$$desired_key = '';
				}else{
					$$desired_key = $tag[$desired_key];
				}
				$columns = $columns.$desired_key.',';
				$values = $values."'".$$desired_key."',";
			}
			$query = "INSERT INTO tags(".trim($columns,',').") VALUES(".trim($values,',').")";
			if(!empty($tag)){
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				$success = array('status' => "Success", "msg" => "Tag Created Successfully.", "data" => $tag);
				$this->response($this->json($success),200);
			}else
				$this->response('',204);	//"No Content" status
		}
		
		private function updateTag(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$tag = json_decode(file_get_contents("php://input"),true);
			$id = (int)$tag['id'];
			$column_names = array('tag_name');
			$keys = array_keys($tag['tag']);
			$columns = '';
			$values = '';
			foreach($column_names as $desired_key){ // Check the customer received. If key does not exist, insert blank into the array.
			   if(!in_array($desired_key, $keys)) {
			   		$$desired_key = '';
				}else{
					$$desired_key = $tag['tag'][$desired_key];
				}
				$columns = $columns.$desired_key."='".$$desired_key."',";
			}
			$query = "UPDATE tags SET ".trim($columns,',')." WHERE tag_id=$id";
			if(!empty($tag)){
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				$success = array('status' => "Success", "msg" => "Tag ".$id." Updated Successfully.", "data" => $tag);
				$this->response($this->json($success),200);
			}else
				$this->response('',204);	// "No Content" status
		}
		
		private function deleteTag(){
			if($this->get_request_method() != "DELETE"){
				$this->response('',406);
			}
			$id = (int)$this->_request['id'];
			if($id > 0){				
				$query="DELETE FROM tags WHERE tag_id = $id";
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				$success = array('status' => "Success", "msg" => "Successfully deleted one record.");
				$this->response($this->json($success),200);
			}else
				$this->response('',204);	// If no records "No Content" status
		}
		
		
		
		
		
//Subject API CALLS

		private function subjects(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$query="SELECT distinct s.subject_id, s.subject_name FROM subjects s order by s.subject_id desc";
			$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

			if($r->num_rows > 0){
				$result = array();
				while($row = $r->fetch_assoc()){
					$result[] = $row;
				}
				$this->response($this->json($result), 200); // send user details
			}
			$this->response('',204);	// If no records "No Content" status
		}
		
		private function subject(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$id = (int)$this->_request['id'];
			if($id > 0){	
				$query="SELECT distinct s.subject_id, s.subject_name FROM subjects s where s.subject_id=$id";
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				if($r->num_rows > 0) {
					$result = $r->fetch_assoc();	
					$this->response($this->json($result), 200); // send user details
				}
			}
			$this->response('',204);	// If no records "No Content" status
		}
		
		private function insertSubject(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}

			$subject = json_decode(file_get_contents("php://input"),true);
			$column_names = array('subject_name');
			$keys = array_keys($subject);
			$columns = '';
			$values = '';
			foreach($column_names as $desired_key){ // Check the customer received. If blank insert blank into the array.
			   if(!in_array($desired_key, $keys)) {
			   		$$desired_key = '';
				}else{
					$$desired_key = $subject[$desired_key];
				}
				$columns = $columns.$desired_key.',';
				$values = $values."'".$$desired_key."',";
			}
			$query = "INSERT INTO subjects(".trim($columns,',').") VALUES(".trim($values,',').")";
			if(!empty($subject)){
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				$success = array('status' => "Success", "msg" => "Subject Created Successfully.", "data" => $subject);
				$this->response($this->json($success),200);
			}else
				$this->response('',204);	//"No Content" status
		}
		
		private function updateSubject(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$subject = json_decode(file_get_contents("php://input"),true);
			$id = (int)$subject['id'];
			$column_names = array('subject_name');
			$keys = array_keys($subject['subject']);
			$columns = '';
			$values = '';
			foreach($column_names as $desired_key){ // Check the customer received. If key does not exist, insert blank into the array.
			   if(!in_array($desired_key, $keys)) {
			   		$$desired_key = '';
				}else{
					$$desired_key = $tag['subject'][$desired_key];
				}
				$columns = $columns.$desired_key."='".$$desired_key."',";
			}
			$query = "UPDATE subjects SET ".trim($columns,',')." WHERE subject_id=$id";
			if(!empty($subject)){
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				$success = array('status' => "Success", "msg" => "Subject ".$id." Updated Successfully.", "data" => $subject);
				$this->response($this->json($success),200);
			}else
				$this->response('',204);	// "No Content" status
		}
		
		private function deleteSubject(){
			if($this->get_request_method() != "DELETE"){
				$this->response('',406);
			}
			$id = (int)$this->_request['id'];
			if($id > 0){				
				$query="DELETE FROM subjects WHERE subject_id = $id";
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				$success = array('status' => "Success", "msg" => "Successfully deleted one record.");
				$this->response($this->json($success),200);
			}else
				$this->response('',204);	// If no records "No Content" status
		}
		
		
		
		
	//Questions API CALLS

		private function questions(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$query="SELECT distinct q.question_id, q.question_title,q.options,q.tag,q.subject,q.complexity,q.status FROM questions q order by q.question_id desc";
			$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

			if($r->num_rows > 0){
				$result = array();
				while($row = $r->fetch_assoc()){
					$result[] = $row;
				}
				$this->response($this->json($result), 200); // send user details
			}
			$this->response('',204);	// If no records "No Content" status
		}
		
		private function question(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$id = (int)$this->_request['id'];
			if($id > 0){	
				$query="SELECT distinct q.question_id, q.question_title,q.options,q.tag,q.subject,q.complexity,q.status FROM questions q where q.question_id=$id";
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				if($r->num_rows > 0) {
					$result = $r->fetch_assoc();	
					$this->response($this->json($result), 200); // send user details
				}
			}
			$this->response('',204);	// If no records "No Content" status
		}
		
		private function insertQuestion(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}

			$question = json_decode(file_get_contents("php://input"),true);
			$column_names = array('question_title','options','correct_answer','tag','subject','complexity','status');
			$keys = array_keys($question);
			$columns = '';
			$values = '';
			foreach($column_names as $desired_key){ // Check the customer received. If blank insert blank into the array.
			   if(!in_array($desired_key, $keys)) {
			   		$$desired_key = '';
				}else{
					$$desired_key = $question[$desired_key];
				}
				$columns = $columns.$desired_key.',';
				$values = $values."'".$$desired_key."',";
			}
			$query = "INSERT INTO questions(".trim($columns,',').") VALUES(".trim($values,',').")";
			if(!empty($question)){
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				$success = array('status' => "Success", "msg" => "Question Created Successfully.", "data" => $question);
				$this->response($this->json($success),200);
			}else
				$this->response('',204);	//"No Content" status
		}
		
		private function updateQuestion(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$question = json_decode(file_get_contents("php://input"),true);
			$id = (int)$question['id'];
			$column_names = array('question_title','options','correct_answer','tag','subject','complexity','status');
			$keys = array_keys($question['question']);
			$columns = '';
			$values = '';
			foreach($column_names as $desired_key){ // Check the customer received. If key does not exist, insert blank into the array.
			   if(!in_array($desired_key, $keys)) {
			   		$$desired_key = '';
				}else{
					$$desired_key = $question['question'][$desired_key];
				}
				$columns = $columns.$desired_key."='".$$desired_key."',";
			}
			$query = "UPDATE questions SET ".trim($columns,',')." WHERE question_id=$id";
			if(!empty($question)){
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				$success = array('status' => "Success", "msg" => "Question ".$id." Updated Successfully.", "data" => $question);
				$this->response($this->json($success),200);
			}else
				$this->response('',204);	// "No Content" status
		}
		
		private function deleteQuestion(){
			if($this->get_request_method() != "DELETE"){
				$this->response('',406);
			}
			$id = (int)$this->_request['id'];
			if($id > 0){				
				$query="DELETE FROM questions WHERE question_id = $id";
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				$success = array('status' => "Success", "msg" => "Successfully deleted one record.");
				$this->response($this->json($success),200);
			}else
				$this->response('',204);	// If no records "No Content" status
		}
		
		private function getTagOptions() {    
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$query="SELECT distinct t.tag_id, t.tag_name FROM tags t order by t.tag_id desc";
			$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

			if($r->num_rows > 0){
				$result = array();
				while($row = $r->fetch_assoc()){
					$result[] = $row;
				}
				$this->response($this->json($result), 200); // send user details
			}
			$this->response('',204);	// If no records "No Content" status
				
				
		}
		
				/**  Function to data from tblProjectType **/
		private function getSubjectOptions(){
		if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$query="SELECT distinct s.subject_id, s.subject_name FROM subjects s order by s.subject_id desc";
			$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

			if($r->num_rows > 0){
				$result = array();
				while($row = $r->fetch_assoc()){
					$result[] = $row;
				}
				$this->response($this->json($result), 200); // send user details
			}
			$this->response('',204);	// If no records "No Content" status
		
		}

		
		/*
		 *	Encode array into JSON
		*/
		private function json($data){
			if(is_array($data)){
				return json_encode($data);
			}
		}
	}
	
	// Initiiate Library
	
	$api = new API;
	$api->processApi();
?>
