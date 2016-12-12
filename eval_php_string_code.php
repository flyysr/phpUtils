<?php

//业务需要：从数据库中读出字符串的php代码
function mysql_get($id){
    return '<?php $i = '.$id.';
            echo "contextValue: ".$contextName."\n";
            echo "hello $i <br/>"; 
            ';
}

//自定义协议
class VariableStream {
    private $string;
    private $position;
    public function stream_open($path, $mode, $options, &$opened_path) {
        $url = parse_url($path);
        $id = $url["host"];

        //根据ID到数据库中取出php字符串代码
        $this->string = mysql_get($id);
        $this->position = 0;
        return true;
    }
    public function stream_read($count) {
        $ret = substr($this->string, $this->position, $count);
        $this->position += strlen($ret);
        return $ret;
    }
    public function stream_eof() {}
    public function stream_stat() {}
}

stream_wrapper_register("var", "VariableStream");

//上下文变量
$contextName = "1000";
//include字符串php代码。（php代码是从数据库中读出来，这里传入的199是数据库的主键ID）
include("var://199");

//修改上下文变量
$contextName = "2000";
//引入另一个字符串php代码
include("var://299");