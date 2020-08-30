<?php

namespace HyperfPlus\Command;

use HyperfPlus\Log\Log;
use HyperfPlus\Log\StdoutLog;
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

        // 生成 CreateAction
        $this->generateActionByTableStructure($connection, $table, $module, 'Create');
        // 生成 UpdateAction
        $this->generateActionByTableStructure($connection, $table, $module, 'Update');
        // 生成 SearchAction
        $this->generateActionByTableStructure($connection, $table, $module, 'Search');
        // 生成 FindAction
        $this->generateActionByTableStructure($connection, $table, $module, 'Find');
        // 生成 Logic
        $this->generateLogic($module);
        // 生成 Service
        $this->generateService($module);
        // 生成 Dao
        $this->generateDao($connection, $table, $module);
        // 生成 Constant
        $this->generateConstant($module);

        StdoutLog::print("{$module}模块已生成！");
    }

    /**
     * 根据表结构生成控制器
     *
     * @param $connection
     * @param $table
     * @param $module
     * @param $action
     */
    private function generateActionByTableStructure($connection, $table, $module, $action)
    {
        $res            = Db::connection($connection)->select('SHOW FULL COLUMNS FROM ' . $table);
        $columnInfoList = Util::object2Array($res);

        $index          = 0;
        $ruleStr        = '';

        // 字段最大长度
        $maxFieldLength = 0;
        foreach ($columnInfoList as $k => $v) {
            if (strlen($v['Field']) > $maxFieldLength) $maxFieldLength = strlen($v['Field']);
        }

        // 计算出 => 之前的字符串长度
        if (($maxFieldLength + 10) % 4 == 0) {
            $preStrLength = $maxFieldLength + 4 + 12;
        } else {
            $preStrLength = (ceil($maxFieldLength / 4) + 1) * 4 + 12;
        }

        foreach ($columnInfoList as $k => $v) {
            // ctime、mtime
            if (in_array($v['Field'], ['ctime', 'mtime'])) continue;

            // id
            if ($v['Field'] == 'id' && $action == 'Create') continue;

            // FindAction 只需要 id
            if ($action == 'Find' && $v['Field'] != 'id') continue;

            $rule = '';

            // CreateAction 和 UpdateAction 需要 required 属性
            if (in_array($action, ['Create', 'Update']) && isset($v['Null']) && $v['Null'] === 'NO') {
                $rule = 'required|';
            }

            // FindAction 的 id 需要 required 属性
            if ($action == 'Find') $rule = 'required|';

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
            $fillSpaceStr = str_repeat(' ', $preStrLength - 10 - strlen($v['Field']));

            if ($index == 0) {
                $ruleStr .= sprintf("'%s'" . $fillSpaceStr . "=> '%s'", $v['Field'], $rule);
            } else {
                $ruleStr .= sprintf(",\n" . str_repeat(' ', 8) . "'%s'" . $fillSpaceStr . "=> '%s'", $v['Field'], $rule);
            }

            $index++;
        }

        $path           = BASE_PATH . "/app/Module/$module/Action/{$action}Action.php";
        $templateStr    = file_get_contents(dirname(__DIR__) . "/Template/{$action}ActionTemplate");
        $contents       = sprintf($templateStr, $module, $module, $module, $module, $ruleStr);
        file_put_contents($path, $contents);
    }

    /**
     * 生成 Logic
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
     * 生成 Service
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
     * 生成 Dao
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
     * 生成 Constant
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

        if (!is_dir(BASE_PATH . "/app/Module")) mkdir(BASE_PATH . "/app/Module");

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