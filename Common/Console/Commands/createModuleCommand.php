<?php

namespace OlaHub\DesignerCorner\commonData\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class createModuleCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:module';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Serve the application on the PHP development server";

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle() {

        //$newModuleName single from module name
        //$moduleName plural from the name
        //$mTableName new module table

        $newModuleName = $this->input->getArgument('moduleName');
        if (!$newModuleName) {
            return $this->error(sprintf("\n\nYou should write new module name\n"));
        }

        $defaultName = "Example";
        $pluralDefaultName = $defaultName."s";
        $dTableName = 'example_table';
        $mode = 0775;
        $file = base_path('Modules');
        if (!file_exists($file)) {
            $moduleFile = mkdir($file, $mode, TRUE);
        }
        $moduleName = \OlaHub\DesignerCorner\commonData\Helpers\InflectLibrary::pluralize($newModuleName);
        $mTableName = $moduleName;
        $newModule = base_path("Modules/". ucfirst($moduleName));
        if (file_exists($newModule)) {
            return $this->error(sprintf("\n\nThe module ($moduleName) already exist\n"));
        }
        $old_umask = umask(0);
        mkdir($newModule, $mode);
        
        mkdir("$newModule/Controllers", $mode);
        $controllerName = ucfirst($moduleName);
        $singularControllerName = ucfirst($newModuleName);
        $file = fopen("$newModule/Controllers/$controllerName" . "Controller.php", "w+");
        $controllerContent = file_get_contents(__DIR__ . "/templatesData/Controllers/$pluralDefaultName" . "Controller.php");
        $controllerContent = str_replace([$pluralDefaultName,$defaultName,$dTableName,"ModuleName"], [$controllerName,$singularControllerName,$mTableName, ucfirst($moduleName)], $controllerContent);
        fwrite($file, $controllerContent);
        fclose($file);

        mkdir("$newModule/Models", $mode);
        $modelName = ucfirst($moduleName);
        $singularModelName = ucfirst($newModuleName);
        $file = fopen("$newModule/Models/$singularModelName" . ".php", "w+");
        $modelContent = file_get_contents(__DIR__."/templatesData/Models/$defaultName" . ".php");
        $modelContent = str_replace([$pluralDefaultName,$defaultName,$dTableName,"ModuleName"], [$modelName,$singularModelName,$mTableName, ucfirst($moduleName)], $modelContent);
        fwrite($file, $modelContent);
        fclose($file);
        
        mkdir("$newModule/ResponseHandlers", $mode);
        $HandlerName = ucfirst($moduleName);
        $singularHandlerName = ucfirst($newModuleName);
        $file = fopen("$newModule/ResponseHandlers/$HandlerName" . "Handler.php", "w+");
        $HandlerContent = file_get_contents(__DIR__."/templatesData/ResponseHandlers/$pluralDefaultName" . "Handler.php");
        $HandlerContent = str_replace([$pluralDefaultName,$defaultName,$dTableName,"ModuleName"], [$HandlerName,$singularHandlerName,$mTableName, ucfirst($moduleName)], $HandlerContent);
        fwrite($file, $HandlerContent);
        fclose($file);
        
        mkdir("$newModule/Routes", $mode);
        $file = fopen("$newModule/Routes/". ucfirst($moduleName)."Routes.php", "w+");
        $RouteContent = file_get_contents(__DIR__."/templatesData/Routes/". ucfirst($pluralDefaultName)."Routes.php");
        $RouteContent = str_replace([$pluralDefaultName,$defaultName,$dTableName,"ModuleName"], [$controllerName,$singularControllerName,$mTableName, ucfirst($moduleName)], $RouteContent);
        fwrite($file, $RouteContent);
        fclose($file);
        
        mkdir("$newModule/Helpers", $mode);
        $helperName = ucfirst($moduleName);
        $singularHelperName = ucfirst($newModuleName);
        $file = fopen("$newModule/Helpers/$helperName" . "Helper.php", "w+");
        $helperContent = file_get_contents(__DIR__."/templatesData/Helpers/$pluralDefaultName" . "Helper.php");
        $helperContent = str_replace([$pluralDefaultName,$defaultName,$dTableName,"ModuleName"], [$helperName,$singularHelperName,$mTableName, ucfirst($moduleName)], $helperContent);
        fwrite($file, $helperContent);
        fclose($file);
        
        umask($old_umask);
        shell_exec('composer dump-autoload');
        $this->info("New module ($moduleName) has been created successfully");
    }

    /**
     * Get the console command Required.
     *
     * @return array
     */
    protected function getArguments() {
        return array(
            array('moduleName', null, InputOption::VALUE_REQUIRED, false),
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions() {
        return array(
            array('host', null, InputOption::VALUE_OPTIONAL, 'The host address to serve the application on.', 'localhost'),
            array('port', null, InputOption::VALUE_OPTIONAL, 'The port to serve the application on.', 8000),
        );
    }

}
