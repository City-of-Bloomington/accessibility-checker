<?php
/**
 * @copyright 2023 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Domain;

class Pdf
{
	/**
	 * Generates a PDF from a source file
	 *
	 * Apache must have permission to write to the SITE_HOME directory.
     *
	 * LibreOffice is a desktop application and will generate the standard
     * user config files.  The config files are required for proper execution.
     * These would normally go into a user's HOME directory; however, when run
     * from PHP as the Apache user, this HOME directory does not exist.
     * You must provide the path where you want LibreOffice to generate the
     * config files.
	 *
	 * @param  string $file   Full path to the source file
     * @param  string $config Full path to libreoffice user configration directory
     * @return string         Full path to the generated PDF
	 */
	public static function convertToPDF(string $file, string $config): string
	{
        if (is_file($file) && is_writable($file)) {
            $info = pathinfo($file);
            $cmd  = "libreoffice -env:UserInstallation=file://$config --convert-to pdf --headless --outdir $info[dirname] $file";
            $out  = "$cmd\n";
            $out .= shell_exec($cmd);
            file_put_contents("$info[dirname]/$info[filename].log", $out);

            if (!is_file("$info[dirname]/$info[filename].pdf")) {
                throw new \Exception("file/pdfConversionFailed");
            }

            return "$info[dirname]/$info[filename].pdf";
        }
        else {
            throw new \Exception("file/invalidSourceFile");
        }
	}

	public static function validate(string $file): string
    {
        return shell_exec(SITE_HOME."/verapdf/verapdf -f ua1 --format html $file");
    }
}
