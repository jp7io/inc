<?
/**
 * Instancia registros da tabela interadmin_cliente
 *
*/
class InterAdmin{
	public $id;
	public $id_tipo;
	/**
	 * @param int $id
	 * @param varchar $_db_prefix
	 * @return object
	 */
	function __construct($id, $_db_prefix=''){
		$this->id=$id;
		$this->db_prefix=($_db_prefix)?$_db_prefix:$GLOBALS['db_prefix'];
	}
	function __get($var){
		if($var == 'id'){
			return $this->$var;
		}
	}
	function __toString(){
		return $this->id;
	}	
	/**
	 * @return mixed
	 */
	function getFieldsValues($fields, $forceAsString=false){   
		/*if(!$this->fieldsValues)*/$this->fieldsValues=jp7_fields_values($this->db_prefix , 'id', $this->id, $fields);
		if($forceAsString){
			foreach($this->fieldsValues as $key=>$value){
				if(strpos($key,"select_")===0)$this->fieldsValuesAsString->$key=jp7_fields_values($this->db_prefix , 'id', $value, 'varchar_key');
				else $this->fieldsValuesAsString->$key=$value;
			}
			return $this->fieldsValuesAsString;
		}else{
			return $this->fieldsValues;
		}
	}
	/**
	 * @return object
	 */
	function getTipo(){
		if(!$this->id_tipo)$this->id_tipo=new InterAdminTipo($this->getFieldsValues('id_tipo'), $this->db_prefix);
		return $this->id_tipo;
	}
	/**
	 * @param int $id_tipo
	 * @return array
	 */
	function getChildren($id_tipo){
		global $db;
		global $jp7_app;
		$sql="SELECT id FROM ".$this->db_prefix.
		" WHERE id_tipo=".$id_tipo.
		((!$jp7_app) ? " AND deleted<>'S'" : "").
		" AND parent_id=".$this->id;
		$rs=mysql_query($sql)or die(mysql_error());
		while($row=mysql_fetch_array($rs)){
			$interadmins[]=new InterAdmin($row['id'], $this->db_prefix);
		}
		mysql_free_result($rs);
		return $interadmins;
	}
	/**
	 * @return string
	 */
	function getURL(){
		return $this->getTipo()->getURL().'?id='.$this->id;
	}
	
}

/**
 * Instancia registros da tabela interadmin_cliente_tipos
 *
 */
class InterAdminTipo{
	public $id;
	public $id_tipo;
	/**
	 * @param int $id_tipo
	 * @param varchar $_db_prefix
	 */
	function __construct($id_tipo, $_db_prefix=''){
		$this->id_tipo=$id_tipo;
		$this->db_prefix=($_db_prefix)?$_db_prefix:$GLOBALS['db_prefix'];
	}
	function __toString(){
		return $this->id_tipo;
	}
	/**
	 * @return mixed
	 */
	function getFieldsValues($fields){
		return jp7_fields_values($this->db_prefix.'_tipos', 'id_tipo', $this->id_tipo, $fields);
	}
	/**
	 * @return object
	 */
	function getParent(){
		$parent=$this->getFieldsValues('parent_id_tipo');
		return (($parent)?(new InterAdminTipo($parent, $this->db_prefix)):false);
	}
	/**
	 * @return array
	 */
	function getChildren(){
		global $db;
		$sql="SELECT id_tipo FROM ".$this->db_prefix."_tipos".
		" WHERE parent_id_tipo=".$this->id_tipo;
		$rs=mysql_query($sql)or die(mysql_error());
		while($row=mysql_fetch_array($rs)){
			$interadminsTipos[]=new InterAdminTipo($row['id_tipo'], $this->db_prefix);
		}
		mysql_free_result($rs);
		return $interadminsTipos;
	}
	/**
	 * @return array
	 */
	function getInterAdmins(){
		global $db;
		$sql="SELECT id FROM ".$this->db_prefix.
		" WHERE id_tipo=".$this->id_tipo;
		$rs=mysql_query($sql)or die(mysql_error());
		while($row=mysql_fetch_array($rs)){
			$interadmins[]=new InterAdmin($row['id'], $this->db_prefix);
		}
		mysql_free_result($rs);
		return $interadmins;
	}
	/**
	 * @return ?
	 */
	function getCampos(){
		return interadmin_tipos_campos($this->getFieldsValues('campos'));
	}
	/**
	 * @return string
	 */
	function getURL(){
		$url='';
		$url_arr='';
		$parent=$this;
		while($parent){
			$url_arr[]=toId($parent->getFieldsValues('nome'));
			$parent=$parent->getParent();
		}
		$url_arr=array_reverse($url_arr);
		$url=join("_",$url_arr);
		$url=substr_replace($url,'/',strpos($url,'_'),1);
		$url.=(count($url_arr)>1)?'.php':'/';
		return $url;
	}
}

class Blabla extends InterAdmin{
	function __construct($id=1){
		$this->id=$id;
		$this->db_prefix=($_db_prefix)?$_db_prefix:$GLOBALS['db_prefix'];
	}
}
?>
