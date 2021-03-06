<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit457f0b857db3c2c957279827c462895b
{
    public static $prefixLengthsPsr4 = array (
        'O' => 
        array (
            'Overtrue\\Pinyin\\' => 16,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Overtrue\\Pinyin\\' => 
        array (
            0 => __DIR__ . '/..' . '/overtrue/pinyin/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit457f0b857db3c2c957279827c462895b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit457f0b857db3c2c957279827c462895b::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
