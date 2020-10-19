<?php
namespace fast;
/**
 * 分享请保持网址。尊重别人劳动成果。谢谢。
 * 图片压缩类：通过缩放来压缩。如果要保持源图比例，把参数$percent保持为1即可。
 * 即使原比例压缩，也可大幅度缩小。数码相机4M图片。也可以缩为700KB左右。如果缩小比例，则体积会更小。
 * 结果：可保存、可直接显示。
 */
class Img {
    /**
     * 图片压缩
     * @param $src 源图
     * @param float $percent  压缩比例
     */
    public function __construct($srcImageName, $maxWidth=0, $maxHeight=0, $dstImageName='', $addLogo='')
    {
        list($srcWidth, $srcHeight, $srcType) = getimagesize($srcImageName);
        $width  = $srcWidth;
        $height = $srcHeight;
        if ($height > $maxHeight) {
            $width  = ($maxHeight / $height) * $width;
            $height = $maxHeight;
        }
        if ($width > $maxWidth) {
            $height = ($maxWidth / $width) * $height;
            $width  = $maxWidth;
        }
        switch ($srcType) {
            case 1:
                $srcImageRsc = imagecreatefromgif($srcImageName);
                break;
            case 2:
                $srcImageRsc = imagecreatefromjpeg($srcImageName);
                break;
            case 3:
                $srcImageRsc = imagecreatefrompng($srcImageName);
                break;
        }
        $dstImageRsc = imagecreatetruecolor($width, $height);
        // 填充透明颜色
        imagefill($dstImageRsc, 0, 0, imagecolorallocatealpha($dstImageRsc, 0, 0, 0, 127));
        // 保存图片时保存透明信息
        imagesavealpha($dstImageRsc, true);
        // 调整大小
        imagecopyresampled($dstImageRsc, $srcImageRsc, 0, 0, 0, 0, $width, $height, $srcWidth, $srcHeight);
        //添加logo
        if($addLogo) {
            $white = imagecolorallocate($dstImageRsc,255,255,255);
            $black = imagecolorallocate($dstImageRsc,50,50,50);
            imagecolortransparent($dstImageRsc,$white);
            //加投影
            imagettftext($dstImageRsc,18,0,($width/2 - strlen($addLogo) * 6) - 1,
                ($height/2 - strlen($addLogo) * 0.6) - 1, $white,
                ROOT_PATH .'/assets/fonts/msyh.ttf', $addLogo); //字体设置部分linux和windows的路径可能不同
            //纯白文字
            imagettftext($dstImageRsc,18,0,$width/2 - strlen($addLogo) * 6,
                $height/2 - strlen($addLogo) * 0.7, $black,
                ROOT_PATH .'/assets/fonts/msyh.ttf', $addLogo); //字体设置部分linux和windows的路径可能不同
        }
        // 保存图片
        switch ($srcType) {
            case 1:
                $res = imagegif($dstImageRsc, $dstImageName);
                break;
            case 2:
                $res = imagejpeg($dstImageRsc, $dstImageName);
                break;
            case 3:
                $res = imagepng($dstImageRsc, $dstImageName);
                break;
        }
        // 释放资源
        imagedestroy($srcImageRsc);
        imagedestroy($dstImageRsc);
        return $res;
    }

}

//$source = '1.png';
//$dst_img = '../copy1.png'; //可加存放路径
//$percent = 1;  #原图压缩，不缩放
//$image = (new Img($source,$percent, $newWidth, $newHeight))->compressImg($dst_img);