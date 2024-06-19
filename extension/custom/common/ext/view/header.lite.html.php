<?php
if($extView = $this->getExtViewFile(__FILE__)){include $extView; return helper::cd();}
$clientLang   = $app->getClientLang();
$webRoot      = $this->app->getWebRoot();
$jsRoot       = $webRoot . "js/";
$themeRoot    = $webRoot . "theme/";
$defaultTheme = $webRoot . 'theme/default/';
$langTheme    = $themeRoot . 'lang/' . $clientLang . '.css';
$clientTheme  = $this->app->getClientTheme();
$onlybody     = zget($_GET, 'onlybody', 'no');
$commonLang   = array('zh-cn', 'zh-tw', 'en', 'fr', 'de');
?>
<!DOCTYPE html>
<html lang='<?php echo $clientLang;?>'>
<head>
  <meta charset='utf-8'>
  <meta http-equiv='X-UA-Compatible' content='IE=edge'>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="renderer" content="webkit">
  <?php
  echo html::title($title . ' - ' . $lang->zentaoPMS);
  js::exportConfigVars();
  if($config->debug)
  {
      $timestamp = time();

      css::import($themeRoot . 'zui/css/min.css?t=' . $timestamp);
      css::import($defaultTheme . 'style.css?t=' . $timestamp);

      css::import($langTheme);
      if(strpos($clientTheme, 'default') === false) css::import($clientTheme . 'style.css?t=' . $timestamp);

      js::import($jsRoot . 'jquery/lib.js');
      js::import($jsRoot . 'zui/min.js?t=' . $timestamp);
      if(!in_array($clientLang, $commonLang)) js::import($jsRoot . 'zui/lang.' . $clientLang . '.min.js?t=' . $timestamp);
      js::import($jsRoot . 'my.full.js?t=' . $timestamp);

  }
  else
  {
      $minCssFile = $defaultTheme . $this->cookie->lang . '.' . $this->cookie->theme . '.css';
      if(!file_exists($this->app->getThemeRoot() . 'default/' . $this->cookie->lang . '.' . $this->cookie->theme . '.css')) $minCssFile = $defaultTheme . 'en.' . $this->cookie->theme . '.css';
      css::import($minCssFile);
      js::import($jsRoot . 'all.js');
      if(!in_array($clientLang, $commonLang)) js::import($jsRoot . 'zui/lang.' . $clientLang . '.min.js');
  }
  if($this->app->getViewType() == 'xhtml') css::import($defaultTheme . 'x.style.css');

  if(defined('IN_USE') and commonModel::isTutorialMode())
  {
      $wizardModule    = defined('WIZARD_MODULE') ? WIZARD_MODULE : $this->moduleName;
      $wizardMethod    = defined('WIZARD_METHOD') ? WIZARD_METHOD : $this->methodName;
      $requiredFields  = '';
      if(isset($config->$wizardModule->$wizardMethod->requiredFields)) $requiredFields = str_replace(' ', '', $config->$wizardModule->$wizardMethod->requiredFields);
      echo "<script>window.TUTORIAL = {'module': '$wizardModule', 'method': '$wizardMethod', tip: '$lang->tutorialConfirm'}; if(config) config.requiredFields = '$requiredFields'; $(function(){window.top.checkTutorialState && setTimeout(window.top.checkTutorialState, 500);});</script>";
  }

  if(isset($pageCSS)) css::internal($pageCSS);

  echo html::favicon($webRoot . 'favicon.ico');
  ?>
<!--[if lt IE 10]>
<?php js::import($jsRoot . 'jquery/placeholder/min.js'); ?>
<![endif]-->
<?php
/* Load hook files for current page. */
$extensionRoot = $this->app->getExtensionRoot();
if($this->config->vision != 'open')
{
    $extHookRule  = $extensionRoot . $this->config->edition . '/common/ext/view/header.*.hook.php';
    $extHookFiles = glob($extHookRule);
    if($extHookFiles) foreach($extHookFiles as $extHookFile) include $extHookFile;
}
if($this->config->vision == 'lite')
{
    $extHookRule  = $extensionRoot . $this->config->vision . '/common/ext/view/header.*.hook.php';
    $extHookFiles = glob($extHookRule);
    if($extHookFiles) foreach($extHookFiles as $extHookFile) include $extHookFile;
}
$xuanExtFile = $extensionRoot . 'xuan/common/ext/view/header.xuanxuan.html.hook.php';
if(file_exists($xuanExtFile)) include $xuanExtFile;
?>
<?php
   js::import($jsRoot . 'html2canvas\min.js');
   js::import($jsRoot . 'pdfjs\min.js');
?>
   
<script>
  const HTML2CANVAS_WATERMARK_TEXT = '班牛 ByteNew';
  const HTML2CANVAS_WATERMARK_FILLSTYLE = 'rgba(100,100,100, 0.1)';
  const HTML2CANVAS_OPTIONS = {
      allowTaint: true,
      useCORS: true,
      dpi: 300,  //dpi属性的值为192，表示图像的分辨率
      scale: 2, //scale属性的值为2，表示图像的缩放比例。
      // backgroundColor: "#F1F6FE"  //backgroundColor属性的值为"#F1F6FE"，表示图像的背景颜色。
    };
   const html2img = (ele) => {
        // 设置height和windowwHeight属性，防止只下载当前看到的页面，这样就可以下载完整的内容
        const _ele = ( typeof ele === 'string' ) ? document.querySelector("#"+ele): ele;
        const options = {
          height: _ele.scrollHeight,
          windowHeight: _ele.scrollHeight,
        };
        html2canvas(_ele, {
          ...HTML2CANVAS_OPTIONS, ...options
        }).then(canvas => {

          /* Watermark. */
          const ctx = canvas.getContext('2d');
          ctx.fillStyle = HTML2CANVAS_WATERMARK_FILLSTYLE;
          ctx.fillText(HTML2CANVAS_WATERMARK_TEXT, canvas.width/2, canvas.height/2);
          ctx.fillText(HTML2CANVAS_WATERMARK_TEXT, canvas.width/3, canvas.height/3);

          // blob
          canvas.toBlob(blob => {
            const href = window.URL.createObjectURL(new Blob([blob]))
            const link = document.createElement('a')
            link.href = href
            link.download = Date.now()+'.png'
            document.body.appendChild(link)
            link.click()
            document.body.removeChild(link)
          }, 'image/png')
        })
    }


   function html2pdf(ele) {
      
        // 获取HTML元素
        const _ele = ( typeof ele === 'string' ) ? document.querySelector("#"+ele): ele;
        const options = {
          height: _ele.scrollHeight,
          windowHeight: _ele.scrollHeight,
        };
        // 将元素转换为canvas对象
        html2canvas(_ele, {
          ...HTML2CANVAS_OPTIONS, ...options
        }).then((canvas) => {
          /* Watermark. */
          const ctx = canvas.getContext('2d');
          ctx.fillStyle = HTML2CANVAS_WATERMARK_FILLSTYLE;
          ctx.fillText(HTML2CANVAS_WATERMARK_TEXT, canvas.width/2, canvas.height/2);
          ctx.fillText(HTML2CANVAS_WATERMARK_TEXT, canvas.width/3, canvas.height/3);

            var pageData = canvas.toDataURL('image/jpeg', 1.0);  //将Canvas对象转换为JPEG格式的数据，并将其存储在pageData变量中。1.0表示图片质量
            
            var contentWidth = canvas.width;   //获取Canvas(上面元素id 'layout-wrapper')对象的宽度
            var contentHeight = canvas.height; //获取Canvas(上面元素id 'layout-wrapper')对象的高度
            // 创建jsPDF对象	
            const pdf = new jsPDF('1', 'pt', [contentWidth, contentHeight]); //创建一个新的PDF对象，参数包括页面格式（'1'表示A4纸张）、单位（'pt'）和页面尺寸（[contentWidth, contentHeight]）
            pdf.addImage(pageData, 'JPEG', 0, 0, contentWidth, contentHeight);  //将JPEG格式的图片添加到PDF文件中，图片的左上角坐标为(0, 0)，宽度为contentWidth，高度为contentHeight
            pdf.save(Date.now()+'.pdf');

            // const pdf2 = new jsPDF('1', 'pt');
            // pdf2.addImage(pageData, 'png', 0, 0);  //将JPEG格式的图片添加到PDF文件中，图片的左上角坐标为(0, 0)，宽度为contentWidth，高度为contentHeight
            // pdf2.save(Date.now()+'.pdf');
        });
    }

</script>
</head>
<?php
$bodyClass = $this->app->getViewType() == 'xhtml' ? 'allow-self-open' : '';
if(isset($pageBodyClass)) $bodyClass = $bodyClass . ' ' . $pageBodyClass;
if($this->moduleName == 'index' && $this->methodName == 'index') $bodyClass .= ' menu-' . ($this->cookie->hideMenu ? 'hide' : 'show');
if(strpos($_SERVER['HTTP_USER_AGENT'], 'xuanxuan') !== false) $bodyClass .= ' xxc-embed';
?>
<body class='<?php echo $bodyClass; ?>'>
<?php if($this->app->getViewType() == 'xhtml'):?>
  <style>
    .main-actions-holder {display: none !important;}
  </style>
<?php endif;?>
