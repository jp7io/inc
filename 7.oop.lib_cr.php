<?

/**
 * Classe para criar um objeto com as informações do registro.
 * Esta classe não pode ser instanciada diretamente, apenas herdada (abstract). 
 */	 	
abstract class InterAdminModel{

	/**
	 * Nome da tabela, definido no construct
	 */	 	
	private $tableName;

	/**
	 * objeto do banco
	 */	 	
	private $db;


	/**
	 * Informará quais fields serão utilizados na query
	 */	 	
	protected $fields='*';
	
	/**
	 * Armazenará as informações do registro
	 */	 	
	private $data;
	
	/**
	 * Armazena os dados do registro em $this->data
	 * Quando $id=null os dados deste objeto poderão ser definidos no setData	 
	 * 
	 * @param int $id Id do registro	 	 
	 */	 	
	function __construct($id=null){
		$this->db=$GLOBALS['db'];
		$this->tableName=$GLOBALS['db_prefix'];
		if($id){
			$sql="SELECT ".$this->fields." FROM ".$this->tableName." WHERE id=".$id;
			$rs=mysql_query($sql)or die(jp7_debug(mysql_error(),$sql));
			$this->data=mysql_fetch_array($rs,MYSQL_ASSOC);
			mysql_free_result($rs);
		}
	}
	
	/**
	 * Define os campos
	 */	 	
	function setFields($value){
		$this->fields=$value;
	}
	
	/**
	 * Define os dados do objeto
	 */	 	
	function setData($value){
		$this->data=$value;
	}

	/**
	 * Obtem todas informações do registro
	 * 
	 * @return array	 	 
	 */	 	
	final function getData(){
		return $this->data;
	}
	
	/**
	 * Obtem o valor de um field
	 * 	 
	 * @param strint $field Nome do field 
	 * @return string	 	 
	 */	 	
	final function getField($value){
		return $this->data[$value];
	}
	
	/**
	 * Define o valor de um field
	 * 	 
	 * @param strint $field Nome do field
	 * @param strint $valor Novo valor	  
	 */	 	
	final function setField($field, $value){
		$this->data[$field]=$value;
	}
	
}

/**
 * Classe para criar um objeto para acessar o banco de dados
 * Esta classe não pode ser instanciada diretamente, apenas herdada (abstract). 
 */	 	
abstract class InterAdminDao{

	/**
	 * id_tipo da classe, deve ser obrigatóriamente definido na classe que filha
	 */	 	
	protected $id_tipo;
	
	/**
	 * Nome da tabela, definido no construct
	 */	 	
	private $tableName;

	/**
	 * objeto do banco
	 */	 	
	private $db;

	/**
	 * Nome do campo chave
	 */	 	
	private $fieldKey;
	
	/**
	 * Campos que serão chamados por default nas querys 
	 */	 	
	protected $fields='*';
	
	function __construct(){
		$this->db=$GLOBALS['db'];
		$this->tableName=$GLOBALS['db_prefix'];
		$this->fieldKey='id';
	}
	
	/**
	 * Função para criar um array de objetos
	 * 	  
	 * @param string $modelClass Nome da classe modelo
	 * @param string $fieldIndex Nome do campo que será a chave/indice do array
	 * @return array Retorna um array de objetos DAO	 	 	 
	 */	 	
	function getInterAdminModels($modelClass,$fieldIndex=null){
		$interAdminsModels=array();
		$sql="SELECT {$this->fields} FROM {$this->tableName} WHERE id_tipo={$this->id_tipo}";
		$rs=mysql_query($sql)or die(jp7_debug(mysql_error(),$sql));
		while($data=mysql_fetch_array($rs,MYSQL_ASSOC)){
			$model=new $modelClass;
			$model->setData($data);
			if($fieldIndex){
				$interAdminsModels[$data[$fieldIndex]]=$model;
			}else{
				$interAdminsModels[]=$model;
			}
		}
		mysql_free_result($rs);
		return $interAdminsModels;
	}
	
	/**
	 * Insere um registro
	 * 	  
	 * @param object $objModel Objeto com os dados a serem inseridos
	 */	 	
	function insert($objModel){
		if($objModel){
			$this->db->AutoExecute($this->tableName,$objModel->data,'INSERT');
			if($this->db->ErrorMsg()){
				exit(jp7_debug($this->db->ErrorMsg()));
			}
		}else{
			exit(jp7_debug('Parâmetros inválidos'));
		} 
	}
	
	/**
	 * Atualiza um registro
	 * 	  
	 * @param object $objModel
	 */	 	
	function update($objModel){
		if($objModel && $fieldKeyValue){
			$this->db->AutoExecute($this->tableName,$objModel->data,'UPDATE',"{$this->fieldKey}='".$objModel->data[$this->fieldKey]."'");
			if($this->db->ErrorMsg()){
				exit(jp7_debug($this->db->ErrorMsg()));
			}
		}else{
			exit(jp7_debug('Parâmetros inválidos'));
		} 
	}
	
	/**
	 * Deleta um registro
	 * 	  
	 * @param object $objModel
	 */	 	
	function delete($objModel){
		if($objModel){
			$sql="DELETE FROM $this->tableName WHERE {$this->fieldKey}='".$objModel->data[$this->fieldKey]."'";
			$this->db->Execute($sql)or die(jp7_debug($this->db->ErrorMsg(),$sql));
		}else{
			exit(jp7_debug('Parâmetros inválidos'));
		}
	}
	
	/**
	 * Define os campos das querys
	 */	 	
	function setFields($value){
		$this->fields=$value;
	}
	
	/**
	 * Define o id_tipo
	 */	 	
	function setIdTipo($value){
		$this->id_tipo=$value;
	}	

	
}

?>
