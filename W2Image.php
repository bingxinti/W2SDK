<?php
/**
 * 图片处理函数库文件
 * @package W2
 * @author 琐琐
 * @since 1.0
 * @version 1.0
 */

class W2Image {

    /**
     * 生成水印图
     * @param string 原图
     * @param string 水印图
     * @param int    水印位置(1/2/3/4)
     * @param int    边距
     * @return 生成的图片
     */
    public static function drawWaterMarkImage($p_imgOriginal, $p_imgWatermark, $p_direction, $p_margin){
        if(!file_exists($p_imgOriginal)){ return null; }
        $ext = strtolower(pathinfo($p_imgOriginal, PATHINFO_EXTENSION));
        // if (!isset($ext)||$ext=='')
        // {
            // echo $p_imgOriginal;
            // $data = @getimagesize($srcFile);
            // var_export($data);
            // exit();
        // }
        $imgO = null;
        switch($ext){
            case 'png':
                $imgO = imagecreatefrompng($p_imgOriginal);
                imagesavealpha($imgO,true);
                break;
            case 'gif':
                $imgO = imagecreatefromgif($p_imgOriginal);
                break;
            case 'jpg':
            case 'jpeg':
                $imgO = imagecreatefromjpeg($p_imgOriginal);
                break;
        }
        if ($imgO==null){
            $imgO = imagecreatefrompng($p_imgOriginal);
            imagesavealpha($imgO,true);
        }
        if ($imgO==null){
            $imgO = imagecreatefromgif($p_imgOriginal);
        }
        if ($imgO==null){
            $imgO = imagecreatefromjpeg($p_imgOriginal);
        }

        if (!isset($p_imgWatermark) || !file_exists($p_imgWatermark))
        {
            return $imgO;
        }
        $ext = strtolower(pathinfo($p_imgWatermark, PATHINFO_EXTENSION));
        $imgW = null;
        switch($ext){
            case 'png':
                $imgW = imagecreatefrompng($p_imgWatermark);
                imagesavealpha($imgW,true);
                break;
            case 'gif':
                $imgW = imagecreatefromgif($p_imgWatermark);
                break;
            case 'jpg':
            case 'jpeg':
                $imgW = imagecreatefromjpeg($p_imgWatermark);
                break;
        }
        if($imgO == null || $imgW == null){ return null; }
        $imgW_x = 0;
        $imgW_y = 0;
        $imgW_w = imagesx($imgW);
        $imgW_h = imagesy($imgW);
        $imgO_w = imagesx($imgO);
        $imgO_h = imagesy($imgO);
        $imgO_x = 0;
        $imgO_y = 0;
        switch($p_direction){
            case 1:
                $imgO_x = $p_margin;
                $imgO_y = $p_margin;
                break;
            case 4:
                $imgO_x = $p_margin;
                $imgO_y = $imgO_h - $imgW_h - $p_margin;
                break;
            case 2:
                $imgO_x = $imgO_w - $imgW_w - $p_margin;
                $imgO_y = $p_margin;
                break;
            default:
                $imgO_x = $imgO_w - $imgW_w - $p_margin;
                $imgO_y = $imgO_h - $imgW_h - $p_margin;
                break;
        }
        imagecopy($imgO, $imgW, $imgO_x, $imgO_y, $imgW_x, $imgW_x, $imgW_w, $imgW_h);
        imagedestroy($imgW);
        return $imgO;
    }

    /**
     * 生成水印图, 保存成文件
     * @param string 原图
     * @param string 水印图
     * @param int    水印位置(1/2/3/4)
     * @param int    边距
     * @param string 生成文件
     * @param int    压缩品质，百分制，仅对jpg和png格式文件有效
     */
    public static function buildWaterMarkImage($p_imgOriginal, $p_imgWatermark, $p_direction, $p_margin, $p_imgDist, $p_quality=95){
        $img = W2Image::drawWaterMarkImage($p_imgOriginal, $p_imgWatermark, $p_direction, $p_margin);
        if($img==null){ return; }
        $ext = strtolower(pathinfo($p_imgDist, PATHINFO_EXTENSION));
        switch($ext){
            case 'png':
                imagepng($img, $p_imgDist, intval($p_quality/10));
                break;
            case 'gif':
                imagegif($img, $p_imgDist);
                break;
            case 'jpg':
            case 'jpeg':
                imagejpeg($img, $p_imgDist, $p_quality);
                break;
        }
        imagedestroy($img);
    }

    /**
     * 复制 文件图
     * @param string 原图
     * @param string 生成文件
     * @param int    压缩品质，百分制，仅对jpg和png格式文件有效
     */
    public static function copyImage($p_imgOriginal, $p_imgDist, $p_quality=95){
        W2Image::buildWaterMarkImage($p_imgOriginal, null, null, null, $p_imgDist, $p_quality);
    }

    /**
     * 生成缩略图, 保存成文件
     * @param string 原图
     * @param string 目标文件位置
     * @param int    目标宽度
     * @param int    目标高度
     * @param int    压缩质量
     */
    public static function makePhotoThumb($srcFile,$photo_small,$dstW,$dstH,$p_quality=90) {
    $data = @getimagesize($srcFile);
    if(($data[0]>$dstW && isset($dstW)) || ($data[1]>$dstH && isset($dstH))){
        if (!isset($dstH) && isset($dstW))
        {
            $dstH   =   round($dstW*$data[1]/$data[0]);
        }
        else if (!isset($dstW) && isset($dstH))
        {
            $dstW   =   round($dstH*$data[0]/$data[1]);
        }
        else if($data[0]>$data[1] && isset($dstW)){
            $dstH   =   round($dstW*$data[1]/$data[0]);
        }else if (isset($dstH)){
            $dstW   =   round($dstH*$data[0]/$data[1]);
        }
        else
        {
            copy($srcFile,$photo_small);
        }
    }else{
        copy($srcFile,$photo_small);
    }
    switch ($data[2]) {
        case 1: //图片类型，1是GIF图
            $im = @ImageCreateFromGIF($srcFile);
            break;
        case 2: //图片类型，2是JPG图
            $im = @imagecreatefromjpeg($srcFile);
            break;
        case 3: //图片类型，3是PNG图
            $im = @ImageCreateFromPNG($srcFile);
            break;
    }
    $srcW=ImageSX($im);
    $srcH=ImageSY($im);
    // $ni=imagecreatetruecolor($dstW,$dstH);
    // imagecopyresampled($ni,$im,0,0,0,0,$dstW,$dstH,$srcW,$srcH);
    if(function_exists("imagecopyresampled"))
    {
        $ni = imagecreatetruecolor($dstW,$dstH);
           imagecopyresampled($ni,$im,0,0,0,0,$dstW,$dstH,$srcW,$srcH);
    }
    else
    {
        $ni = imagecreate($dstW,$dstH);
       imagecopyresized($ni,$im,0,0,0,0,$dstW,$dstH,$srcW,$srcH);
    }    
    switch($data[2]) {
        case 3:
            imagepng($ni, $photo_small, intval($p_quality/10));
            break;
        case 1:
            imagegif($ni, $photo_small);
            break;
        case 2:
            imagejpeg($ni, $photo_small, $p_quality);
            break;
    }    
    // imagecopyresized($ni,$im,0,0,0,0,$dstW,$dstH,$srcW,$srcH);
    // ImageJpeg($ni,$photo_small,100);
    //ImageJpeg($ni); //在显示图片时用，把注释取消，可以直接在页面显示出图片。
  }

    /**
         *------------------------------------------------------------------------------
         *                等比例压缩图片
         *------------------------------------------------------------------------------
         * @param String $src_imagename 源文件名        比如 “source.jpg”
         * @param int    $maxwidth      压缩后最大宽度
         * @param int    $maxheight     压缩后最大高度
         * @param String $savename      保存的文件名    “d:save”
         * @param String $filetype      保存文件的格式 比如 ”.jpg“
         * @author Yovae     <yovae@qq.com>
         * @version 1.0
         *------------------------------------------------------------------------------
         */
    public static function resizeImage($src_imagename,$maxwidth,$maxheight,$savename,$filetype)
    {
        $im=imagecreatefromjpeg($src_imagename);
        $current_width = imagesx($im);
        $current_height = imagesy($im);
     
        if(($maxwidth && $current_width > $maxwidth) || ($maxheight && $current_height > $maxheight))
        {
            if($maxwidth && $current_width>$maxwidth)
            {
                $widthratio = $maxwidth/$current_width;
                $resizewidth_tag = true;
            }
     
            if($maxheight && $current_height>$maxheight)
            {
                $heightratio = $maxheight/$current_height;
                $resizeheight_tag = true;
            }
     
            if($resizewidth_tag && $resizeheight_tag)
            {
                if($widthratio<$heightratio)
                    $ratio = $widthratio;
                else
                    $ratio = $heightratio;
            }
     
            if($resizewidth_tag && !$resizeheight_tag)
                $ratio = $widthratio;
            if($resizeheight_tag && !$resizewidth_tag)
                $ratio = $heightratio;
     
            $newwidth = $current_width * $ratio;
            $newheight = $current_height * $ratio;
     
            if(function_exists("imagecopyresampled"))
            {
                $newim = imagecreatetruecolor($newwidth,$newheight);
                   imagecopyresampled($newim,$im,0,0,0,0,$newwidth,$newheight,$current_width,$current_height);
            }
            else
            {
                $newim = imagecreate($newwidth,$newheight);
               imagecopyresized($newim,$im,0,0,0,0,$newwidth,$newheight,$current_width,$current_height);
            }
     
            $savename = $savename.$filetype;
            imagejpeg($newim,$savename);
            imagedestroy($newim);
        }
        else
        {
            $savename = $savename.$filetype;
            imagejpeg($im,$savename);
        }          
    }

}

/**
 * unit test
 */
/*
if(array_key_exists('argv', $GLOBALS) && realpath($argv[0]) == __file__){
    buildWaterMarkImage('/Library/Widgets/Calculator.wdgt/Images/Calculator.png',
        '/Library/Widgets/Calculator.wdgt/Images/comma.png', 3, 10, 'watermarked.png');
    
    header('Content-type: image/jpeg');
    $img = drawWaterMarkImage('ad.jpg','watermark.png', 3, 10);
    imagejpeg($img);
    imagedestroy($img);
}
*/

?>
