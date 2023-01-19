<?php
/**
 * @copyright 2023 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Views;

use Google\Service\Calendar\Events;
use Web\View;

class HomeView extends View
{
    public static $mime_types = [
        'application/msword'                                                      => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/vnd.oasis.opendocument'                                      => 'odf',
        'application/vnd.oasis.opendocument.text'                                 => 'odt',
        'application/pdf'                                                         => 'pdf',
        'application/rtf'                                                         => 'rtf',
		       'text/rtf'                                                         => 'rtf'
    ];

    public function __construct()
    {
        parent::__construct();

        $upload_max_size  = ini_get('upload_max_filesize');
        $post_max_size    = ini_get('post_max_size');
        $upload_max_bytes = self::return_bytes($upload_max_size);
        $post_max_bytes   = self::return_bytes(  $post_max_size);

        if ($upload_max_bytes < $post_max_bytes) {
            $maxSize  = $upload_max_size;
            $maxBytes = $upload_max_bytes;
        }
        else {
            $maxSize  = $post_max_size;
            $maxBytes = $post_max_bytes;
        }

        $accept = [];
        foreach (self::$mime_types as $mime=>$ext) { $accept[] = ".$ext"; }
        $accept = implode(',', $accept);

        $this->vars = [
            'maxSize'  => $maxSize,
            'maxBytes' => $maxBytes,
            'accept'   => $accept
        ];
    }
    public function render(): string
    {
        return $this->twig->render("{$this->outputFormat}/index.twig", $this->vars);
    }

    private static function return_bytes($size)
    {
        switch (substr($size, -1)) {
            case 'M': return (int)$size * 1048576;
            case 'K': return (int)$size * 1024;
            case 'G': return (int)$size * 1073741824;
            default:  return (int)$size;
        }
    }
}
