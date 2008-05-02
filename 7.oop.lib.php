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
	function __construct($id = '', $_db_prefix = ''){
		$this->id = $id;
		$this->db_prefix = ($_db_prefix) ? $_db_prefix : $GLOBALS['db_prefix'];
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
	 * @return mixed
	 */
	function setFieldsValues($fields_values){
		if ($this->id) {
			foreach ($fields_values as $key=>$value) {
				$GLOBALS['setFieldsValues_' . $this->id . '_' . $key] = $value;
			}
			jp7_db_insert($this->db_prefix, 'id', $this->id, 'setFieldsValues_' . $this->id . '_');
		} else {
			foreach ($fields_values as $key=>$value) {
				$GLOBALS['setFieldsValues_' . $key] = $value;
			}
			$this->id = jp7_db_insert($this->db_prefix, 'id', 0, 'setFieldsValues_');
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
	function getChildren($id_tipo,$orderby=""){
		global $db;
		global $jp7_app;
		$sql="SELECT id FROM ".$this->db_prefix.
		" WHERE id_tipo=".$id_tipo.
		((!$jp7_app) ? " AND deleted<>'S'" : "").
		" AND parent_id=".$this->id.
		(($orderby)?" ORDER BY ".$orderby:"");
		$rs = $db->Execute($sql)or die(jp7_debug($db->ErrorMsg(),$sql));
		while($row = $rs->FetchNextObj()){
			$interadmins[]=new InterAdmin($row->id, $this->db_prefix);
		}
		$rs->Close();
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
		$rs = $db->Execute($sql)or die(jp7_debug($db->ErrorMsg(),$sql));
		while($row = $rs->FetchNextObj()){
			$interadminsTipos[]=new InterAdminTipo($row->id_tipo, $this->db_prefix);
		}
		$rs->Close();
		return $interadminsTipos;
	}
	/**
	 * @return array
	 */
	function getInterAdmins($where = null){
		global $db;
		$sql = "SELECT id FROM " . $this->db_prefix.
		" WHERE id_tipo=" . $this->id_tipo;
		if($where) $sql .= " AND ({$where})";
		//$rs = $db->Execute($sql)or die(jp7_debug($db->ErrorMsg(),$sql));
		$rs = $db->Execute($sql)or die(jp7_debug($db->ErrorMsg(),$sql));
		while($row = $rs->FetchNextObj()){
			$interadmins[] = new InterAdmin($row->id, $this->db_prefix);
		}
		$rs->Close();
		return $interadmins;
	}
	/**
	 * @return ?
	 */
	function getCampos(){
		$campos				= $this->getFieldsValues('campos');
		$campos_parameters	= array("tipo", "nome", "ajuda", "tamanho", "obrigatorio", "separador", "xtra", "lista", "orderby", "combo", "readonly", "form", "label", "permissoes", "default");
		$campos				= split("{;}", $campos);
		for($i = 0; $i < count($campos); $i++){
			$parameters = split("{,}", $campos[$i]);
			if($parameters[0]){
				$A[$parameters[0]]['ordem'] = ($i+1);
				$isSelect = (strpos($parameters[0], 'select_') !== false);
				for($j = 0 ; $j < count($parameters); $j++){
					$A[$parameters[0]][$campos_parameters[$j]] = $parameters[$j];
				}
				if($isSelect && $A[$parameters[0]]['nome']!='all'){
					$id_tipo = $A[$parameters[0]]['nome'];
					$Cadastro_r = new InterAdminTipo($id_tipo);	
					$A[$parameters[0]]['children'] = $Cadastro_r->getCampos();
					//jp7_print_r($parameters[0]);
					//jp7_print_r($A[$parameters[0]]['nome']);
				}
			}
		}
		return $A;
		//return interadmin_tipos_campos($this->getFieldsValues('campos'));
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
