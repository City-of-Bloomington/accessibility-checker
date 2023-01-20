<?php
/**
 * @copyright 2023 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);

namespace Web;

use Domain\Pdf;
use Web\Views\HomeView;

class HomeController extends Controller
{
    private const DIR = '/tmp';

    public function __invoke(array $params): View
    {
        if (isset($_FILES['testFile']) && $_FILES['testFile']['error'] != UPLOAD_ERR_NO_FILE) {
            if ( !$_FILES['testFile']['tmp_name']) {
                throw new \Exception('files/uploadFailed');
            }

            $file          = $_FILES['testFile'];
            $filename      = basename($file['name']);

            $mime_type = mime_content_type($_FILES['testFile']['tmp_name']);
            if (array_key_exists($mime_type, HomeView::$mime_types)) {
                $extension = HomeView::$mime_types[$mime_type];
            }
            else {
                throw new \Exception("files/unknownFileType");
            }

            $dir = SITE_HOME.'/files';
            if (!is_dir($dir)) {
                mkdir  ($dir, 0777, true);
            }
            move_uploaded_file($_FILES['testFile']['tmp_name'], "$dir/$filename");
            $pdf = Pdf::convertToPdf("$dir/$filename", SITE_HOME);

        }

        return new HomeView();
    }
}
