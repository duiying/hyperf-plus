<?php

namespace HyperfPlus\Command;

use HyperfPlus\Log\Log;
use HyperfPlus\Util\Util;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Hyperf\DbConnection\Db;

/**
 * 代码生成器
 *
 * @author duiying <wangyaxiandev@gmail.com>
 * @package HyperfPlus\Command
 */

/**
 * @Command
 */
class ModuleGenerator extends HyperfCommand
{
    /**
     * 执行的命令行（php bin/hyperf.php generate:module）
     *
     * @var string
     */
    protected $name = 'generate:module';

    public function configure()
    {
        parent::configure();
        $this->setHelp('模块生成');
        $this->setDescription('模块生成');
    }

    public function handle()
    {
        $connection = $this->ask('请输入数据库名称');
        if (empty(trim($connection))) {
            Log::error('数据库名称不能为空');
            return;
        }

        $table = $this->ask('请输入表名称');
        if (empty(trim($table))) {
            Log::error('表名称不能为空');
            return;
        }

        $module = $this->ask('请输入模块名称');
        if (empty(trim($module))) {
            Log::error('模块名称不能为空');
            return;
        }

        if (is_dir(BASE_PATH . '/app/Module/' . $module)) {
            Log::error(sprintf('模块 %s 已存在，请先删除该模块！', $module));
            return;
        }

        // 生成模块目录结构和相应文件
        $this->generateModuleDirectory($module);

        // 生成CreateAction
        $this->generateCreateAction($connection, $table, $module);
        // 生成UpdateAction
        $this->generateUpdateAction($connection, $table, $module);
        // 生成SearchAction
        $this->generateSearchAction($connection, $table, $module);
        // 生成FindAction
        $this->generateFindAction($connection, $table, $module);
        // 生成Logic
        $this->generateLogic($module);
        // 生成Service
        $this->generateService($module);
        // 生成Dao
        $this->generateDao($connection, $table, $module);
        // 生成Constant
        $this->generateConstant($module);

        Log::info("{$module}模块已生成！");
    }

    /**
     * 生成CreateAction
     *
     * @param $connection
     * @param $table
     * @param $module
     */
    private function generateCreateAction($connection, $table, $module)
    {
        $res            = Db::connection($connection)->select('SHOW FULL COLUMNS FROM ' . $table);
        $columnInfoList = Util::object2Array($res);

        $index      = 0;
        $ruleStr    = '';

        foreach ($columnInfoList as $k => $v) {
            // id、ctime、mtime
            if (in_array($v['Field'], ['id', 'ctime', 'mtime'])) continue;

            $rule = '';
            if (isset($v['Null']) && $v['Null'] === 'NO') {
                $rule = 'required|';
            }
            if (Util::contain($v['Type'], 'int')) {
                $rule .= 'integer';
            } elseif (Util::contain($v['Type'], 'float') || Util::contain($v['Type'], 'double') || Util::contain($v['Type'], 'decimal')) {
                $rule .= 'numeric';
            } elseif (Util::contain($v['Type'], 'datetime')) {
                $rule .= 'date';
            } else {
                $rule .= 'string';
            }

            // 填充空字符串，对齐 => 用
            if (strlen($v['Field']) < 48 - 10) {
                $fillSpaceStr = str_repeat(' ', 48 - 10 - strlen($v['Field']));
            } else {
                $fillSpaceStr = ' ';
            }

            if ($index == 0) {
                $ruleStr .= sprintf("'%s'" . $fillSpaceStr . "=> '%s'", $v['Field'], $rule);
            } else {
                $ruleStr .= sprintf(",\n" . str_repeat(' ', 8) . "'%s'" . $fillSpaceStr . "=> '%s'", $v['Field'], $rule);
            }

            $index++;
        }

        $path           = BASE_PATH . "/app/Module/$module/Action/CreateAction.php";
        $templateStr    = file_get_contents(dirname(__DIR__) . "/Template/CreateActionTemplate");
        $contents       = sprintf($templateStr, $module, $module, $module, $module, $ruleStr);
        file_put_contents($path, $contents);
    }

    /**
     * 生成SearchAction
     *
     * @param $connection
     * @param $table
     * @param $module
     */
    private function generateSearchAction($connection, $table, $module)
    {
        $res            = Db::connection($connection)->select('SHOW FULL COLUMNS FROM ' . $table);
        $columnInfoList = Util::object2Array($res);

        $index      = 0;
        $ruleStr    = '';

        foreach ($columnInfoList as $k => $v) {
            // ctime、mtime
            if (in_array($v['Field'], ['ctime', 'mtime'])) continue;

            $rule = '';

            if (Util::contain($v['Type'], 'int')) {
                $rule .= 'integer';
            } elseif (Util::contain($v['Type'], 'float') || Util::contain($v['Type'], 'double') || Util::contain($v['Type'], 'decimal')) {
                $rule .= 'numeric';
            } elseif (Util::contain($v['Type'], 'datetime')) {
                $rule .= 'date';
            } else {
                $rule .= 'string';
            }

            // 填充空字符串，对齐 => 用
            if (strlen($v['Field']) < 48 - 10) {
                $fillSpaceStr = str_repeat(' ', 48 - 10 - strlen($v['Field']));
            } else {
                $fillSpaceStr = ' ';
            }

            if ($index == 0) {
                $ruleStr .= sprintf("'%s'" . $fillSpaceStr . "=> '%s'", $v['Field'], $rule);
            } else {
                $ruleStr .= sprintf(",\n" . str_repeat(' ', 8) . "'%s'" . $fillSpaceStr . "=> '%s'", $v['Field'], $rule);
            }

            $index++;
        }

        $path           = BASE_PATH . "/app/Module/$module/Action/SearchAction.php";
        $templateStr    = file_get_contents(dirname(__DIR__) . "/Template/SearchActionTemplate");
        $contents       = sprintf($templateStr, $module, $module, $module, $module, $ruleStr);
        file_put_contents($path, $contents);
    }

    /**
     * 生成SearchAction
     *
     * @param $connection
     * @param $table
     * @param $module
     */
    private function generateFindAction($connection, $table, $module)
    {
        $res            = Db::connection($connection)->select('SHOW FULL COLUMNS FROM ' . $table);
        $columnInfoList = Util::object2Array($res);

        $index      = 0;
        $ruleStr    = '';

        foreach ($columnInfoList as $k => $v) {
            // ctime、mtime
            if (in_array($v['Field'], ['ctime', 'mtime'])) continue;

            $rule = '';

            if (Util::contain($v['Type'], 'int')) {
                $rule .= 'integer';
            } elseif (Util::contain($v['Type'], 'float') || Util::contain($v['Type'], 'double') || Util::contain($v['Type'], 'decimal')) {
                $rule .= 'numeric';
            } elseif (Util::contain($v['Type'], 'datetime')) {
                $rule .= 'date';
            } else {
                $rule .= 'string';
            }

            // 填充空字符串，对齐 => 用
            if (strlen($v['Field']) < 48 - 10) {
                $fillSpaceStr = str_repeat(' ', 48 - 10 - strlen($v['Field']));
            } else {
                $fillSpaceStr = ' ';
            }

            if ($index == 0) {
                $ruleStr .= sprintf("'%s'" . $fillSpaceStr . "=> '%s'", $v['Field'], $rule);
            } else {
                $ruleStr .= sprintf(",\n" . str_repeat(' ', 8) . "'%s'" . $fillSpaceStr . "=> '%s'", $v['Field'], $rule);
            }

            $index++;
        }

        $path           = BASE_PATH . "/app/Module/$module/Action/FindAction.php";
        $templateStr    = file_get_contents(BASE_PATH . "/app/Kernel/Template/FindActionTemplate");
        $contents       = sprintf($templateStr, $module, $module, $module, $module, $ruleStr);
        file_put_contents($path, $contents);
    }

    /**
     * 生成CreateAction
     *
     * @param $connection
     * @param $table
     * @param $module
     */
    private function generateUpdateAction($connection, $table, $module)
    {
        $res            = Db::connection($connection)->select('SHOW FULL COLUMNS FROM ' . $table);
        $columnInfoList = Util::object2Array($res);

        $index      = 0;
        $ruleStr    = '';

        foreach ($columnInfoList as $k => $v) {
            // ctime、mtime
            if (in_array($v['Field'], ['ctime', 'mtime'])) continue;

            $rule = '';
            if (isset($v['Null']) && $v['Null'] === 'NO') {
                $rule = 'required|';
            }
            if (Util::contain($v['Type'], 'int')) {
                $rule .= 'integer';
            } elseif (Util::contain($v['Type'], 'float') || Util::contain($v['Type'], 'double') || Util::contain($v['Type'], 'decimal')) {
                $rule .= 'numeric';
            } elseif (Util::contain($v['Type'], 'datetime')) {
                $rule .= 'date';
            } else {
                $rule .= 'string';
            }

            // 填充空字符串，对齐 => 用
            if (strlen($v['Field']) < 48 - 10) {
                $fillSpaceStr = str_repeat(' ', 48 - 10 - strlen($v['Field']));
            } else {
                $fillSpaceStr = ' ';
            }

            if ($index == 0) {
                $ruleStr .= sprintf("'%s'" . $fillSpaceStr . "=> '%s'", $v['Field'], $rule);
            } else {
                $ruleStr .= sprintf(",\n" . str_repeat(' ', 8) . "'%s'" . $fillSpaceStr . "=> '%s'", $v['Field'], $rule);
            }

            $index++;
        }

        $path           = BASE_PATH . "/app/Module/$module/Action/UpdateAction.php";
        $templateStr    = file_get_contents(dirname(__DIR__) . "/Template/UpdateActionTemplate");
        $contents       = sprintf($templateStr, $module, $module, $module, $module, $ruleStr);
        file_put_contents($path, $contents);
    }

    /**
     * 生成Logic
     *
     * @param $module
     */
    private function generateLogic($module)
    {
        $path           = BASE_PATH . "/app/Module/$module/Logic/{$module}Logic.php";
        $templateStr    = file_get_contents(dirname(__DIR__) . "/Template/LogicTemplate");
        $contents       = sprintf($templateStr, $module, $module, $module, $module, $module);
        file_put_contents($path, $contents);
    }

    /**
     * 生成Service
     *
     * @param $module
     */
    private function generateService($module)
    {
        $path           = BASE_PATH . "/app/Module/$module/Service/{$module}Service.php";
        $templateStr    = file_get_contents(dirname(__DIR__) . "/Template/ServiceTemplate");
        $contents       = sprintf($templateStr, $module, $module, $module, $module, $module);
        file_put_contents($path, $contents);
    }

    /**
     * 生成Dao
     *
     * @param $connection
     * @param $table
     * @param $module
     */
    private function generateDao($connection, $table, $module)
    {
        $path           = BASE_PATH . "/app/Module/$module/Dao/{$module}Dao.php";
        $templateStr    = file_get_contents(dirname(__DIR__) . "/Template/DaoTemplate");
        $contents       = sprintf($templateStr, $module, $module, "'$connection'", "'$table'");
        file_put_contents($path, $contents);
    }

    /**
     * 生成Constant
     *
     * @param $module
     */
    private function generateConstant($module)
    {
        $path           = BASE_PATH . "/app/Module/$module/Constant/{$module}Constant.php";
        $templateStr    = file_get_contents(dirname(__DIR__) . "/Template/ConstantTemplate");
        $contents       = sprintf($templateStr, $module, $module);
        file_put_contents($path, $contents);
    }

    /**
     * 生成模块目录结构和相应文件
     *
     * @param $module
     */
    private function generateModuleDirectory($module)
    {
        $directoryList = [
            'Action',
            'Logic',
            'Service',
            'Dao',
            'Constant',
        ];

        mkdir(BASE_PATH . "/app/Module/$module");

        foreach ($directoryList as $k => $v) {
             mkdir(BASE_PATH . "/app/Module/$module/" . $v);
        }

        $fileList = [
            "Action/CreateAction.php",
            "Action/UpdateAction.php",
            "Action/SearchAction.php",
            "Action/FindAction.php",
            "Logic/{$module}Logic.php",
            "Service/{$module}Service.php",
            "Dao/{$module}Dao.php",
            "Constant/{$module}Constant.php",
        ];

        foreach ($fileList as $k => $v) {
            touch(BASE_PATH . "/app/Module/$module/" . $v);
        }
    }
}