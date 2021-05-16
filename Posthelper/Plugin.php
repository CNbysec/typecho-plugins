<?php
/**
 * 后台编辑文章时增加标签选择列表，支持智能插入标签；支持一键插入附件中的所有图片,支持一键插入所有非图片附件,解决全屏状态下鼠标放到附件上传按钮上导致的窗口抖动问题
 * 
 * @package Posthelper
 * @author 泽泽社长
 * @version 1.8
 * @link http://qqdie.com/archives/tyepcho-tag-select-plugin.html
 */
class Posthelper_Plugin implements Typecho_Plugin_Interface
{ 
 public static function activate()
	{
Typecho_Plugin::factory('admin/write-post.php')->bottom = array('Posthelper_Plugin', 'tagslist');
    }
	/* 禁用插件方法 */
	public static function deactivate(){}
    public static function config(Typecho_Widget_Helper_Form $form){
	$autotag = new Typecho_Widget_Helper_Form_Element_Radio('autotag', array(
'0' => _t('关闭'),
'1' => _t('打开'),
 ),'0', _t('自动标签'), _t('自动检测文章内容插入已有标签，10秒自动检测一次'));
	$form->addInput($autotag);
    }
    
    public static function personalConfig(Typecho_Widget_Helper_Form $form){}


    public static function tagslist()
    {
$tag="";$taglist="";$i=0;//循环一次利用到两个位置
Typecho_Widget::widget('Widget_Metas_Tag_Cloud', 'sort=count&desc=1&limit=200')->to($tags);
while ($tags->next()) {
$tag=$tag."'".$tags->name."',";
$taglist=$taglist."<a id=".$i." onclick=\"$(\'#tags\').tokenInput(\'add\', {id: \'".$tags->name."\', tags: \'".$tags->name."\'});\">".$tags->name."</a>";
$i++;
}
?><style>.Posthelper a{cursor: pointer; padding: 0px 6px; margin: 2px 0;display: inline-block;border-radius: 2px;text-decoration: none;}
.Posthelper a:hover{background: #ccc;color: #fff;}.fullscreen #tab-files{right: 0;}/*解决全屏状态下鼠标放到附件上传按钮上导致的窗口抖动问题*/
</style>
<script>
  function chaall () {
   var html='';
 $("#file-list li .insert").each(function(){
   var t = $(this), p = t.parents('li');
   var file=t.text();
   var url= p.data('url');
   var isImage= p.data('image');
   if ($("input[name='markdown']").val()==1) {
   html = isImage ? html+'\n!['+file+'](' + url + ')\n':''+html+'';
   }else{
   html = isImage ? html+'<img src="' + url + '" alt="' + file + '" />\n':''+html+'';
   }
    });
   var textarea = $('#text');
   textarea.replaceSelection(html);return false;
    }

    function chaquan () {
   var html='';
 $("#file-list li .insert").each(function(){
   var t = $(this), p = t.parents('li');
   var file=t.text();
   var url= p.data('url');
   var isImage= p.data('image');
   if ($("input[name='markdown']").val()==1) {
   html = isImage ? html+'':html+'\n['+file+'](' + url + ')\n';
   }else{
   html = isImage ? html+'':html+'<a href="' + url + '"/>' + file + '</a>\n';
   }
    });
   var textarea = $('#text');
   textarea.replaceSelection(html);return false;
    }
function filter_method(text, badword){
    //获取文本输入框中的内容
    var value = text;
    var res = '';
    //遍历敏感词数组
    for(var i=0; i<badword.length; i++){
        var reg = new RegExp(badword[i],"g");
        //判断内容中是否包括敏感词		
        if (value.indexOf(badword[i]) > -1) {
            $('#tags').tokenInput('add', {id: badword[i], tags: badword[i]});
        }
    }
    return;
}
var badwords = [<?php echo $tag; ?>];
function chatag(){
var textarea=$('#text').val();
filter_method(textarea, badwords); 
}
  $(document).ready(function(){
    /*
    $('#file-list').after('<div class="Posthelper"><a class="w-100" onclick=\"chaall()\" style="background: #E9E9E6;text-align: center;padding: 5px 0;color: #1344ff;">插入所有图片</a><a class="w-100" onclick=\"chaquan()\" style="background: #E9E9E6;text-align: center;padding: 5px 0;color: #1344ff;">插入所有非图片附件</a></div>');
    */
    $('#tags').after('<div style="margin-top: 35px;" class="Posthelper"><ul style="list-style: none;border: 1px solid #D9D9D6;padding: 6px 12px; max-height: 240px;overflow: auto;background-color: #FFF;border-radius: 2px;margin-bottom: 0;"><?php echo $taglist; ?></ul><a class="w-100" onclick=\"chatag()\" style="background: #E9E9E6;text-align: center;padding: 5px 0;color: #1344ff;">检测内容插入标签</a></div>');
  }); <?php if(Typecho_Widget::widget('Widget_Options')->plugin('Posthelper')->autotag==1){ ?>setInterval(function () {chatag();}, 10000);<?php }?> 
  </script>
<?php

    }
}