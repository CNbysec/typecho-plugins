<?php
/**
 * 基于 Lightbox v2.7.1 制作。
 * @package Lightbox
 * @author 王And木
 * @version 1.0
 * @link http://www.iwonmo.com
 */
class Lightbox_Plugin implements Typecho_Plugin_Interface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     *
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Archive')->header = array('Lightbox_Plugin', 'headlink');
        Typecho_Plugin::factory('Widget_Abstract_Contents')->contentEx = array('Lightbox_Plugin', 'Lightboxclass');
        Typecho_Plugin::factory('Widget_Abstract_Contents')->excerptEx = array('Lightbox_Plugin', 'Lightboxclass');
    }
   
    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     *
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate(){
	}
   
  
    /**
     * 获取插件配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form){
		/** 正则表达式 **/
        $tempregular = new Typecho_Widget_Helper_Form_Element_Text('regularstr', NULL, "/<img src=('|\")([^>]*).(bmp|gif|jpeg|jpg|png)('|\")(.*?)>/i", _t('匹配图片的正则表达式'));
        $form->addInput($tempregular);
		/** 替换成 **/
		$tempreplace = new Typecho_Widget_Helper_Form_Element_Text('replacetext', NULL,'<p><a href="$2.$3" data-lightbox="example-set" ><img src="$2.$3"/></a>', _t('替换正则表达式[支持后向引用]'));
		$form->addInput($tempreplace);
		$rpmode = new Typecho_Widget_Helper_Form_Element_Radio('rpmode',
			array('temptrue'=>_t('加载'),'tempfalse'=>_t('不加载')),'temptrue',_t('是否加载 Jquery-1.7.2.min.js'),NULL);
		$form->addInput($rpmode);
		
		
	}

    /**
     * 个人用户的配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form){}   

    /**
     * 自动替换图片链接
     *
     * @access public
     * @param string $content
     * @return void
     */
    public static function Lightboxclass($content, $widget, $lastResult) {    
		$regularstr = Typecho_Widget::widget('Widget_Options')->plugin('Lightbox')->regularstr; // 获取正则表达式
		$replacetext = Typecho_Widget::widget('Widget_Options')->plugin('Lightbox')->replacetext; //获取替换字符串
		if ($widget->is('index') || $widget->is('post') || $widget->is('archive') )
			$content = preg_replace($regularstr,$replacetext, $content);
		return $content;
    }
    /**
     * 头部插入CSS
     *
     * @access public
     * @param unknown $headlink
     * @return unknown
     */
    public static function headlink($cssUrl) {
        $Lightbox_ul = Helper::options()->pluginUrl .'/Lightbox/';
		$cssUrl = '<link rel="stylesheet" type="text/css" media="all" href="'.$Lightbox_ul.'lightbox.css" />';
		$link = '<script src="'.$Lightbox_ul.'jquery-1.7.2.min.js"></script>';
		$links = '<script src="'.$Lightbox_ul.'lightbox.min.js"></script>';
		$rpmodes = Typecho_Widget::widget('Widget_Options')->plugin('Lightbox')->rpmode; 
		if($rpmodes == 'temptrue')
			echo $cssUrl,$link,$links;
		else
			echo $cssUrl,$links;

    }
}