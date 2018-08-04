<?php

namespace Admin\Controller;

/**
 * 分类管理
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-01T21:51:08+0800
 */
class GoodsCategoryController extends CommonController
{
	/**
	 * [_initialize 前置操作-继承公共前置方法]
	 * @author   Devil
	 * @blog     http://gong.gg/
	 * @version  0.0.1
	 * @datetime 2016-12-03T12:39:08+0800
	 */
	public function _initialize()
	{
		// 调用父类前置方法
		parent::_initialize();

		// 登录校验
		$this->Is_Login();

		// 权限校验
		$this->Is_Power();
	}

	/**
     * [Index 分类列表]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2016-12-06T21:31:53+0800
     */
	public function Index()
	{
		// 是否启用
		$this->assign('common_is_enable_list', L('common_is_enable_list'));
		$this->display('Index');
	}

	/**
	 * [GetNodeSon 获取节点子列表]
	 * @author   Devil
	 * @blog     http://gong.gg/
	 * @version  0.0.1
	 * @datetime 2016-12-25T15:19:45+0800
	 */
	public function GetNodeSon()
	{
		// 是否ajax请求
		if(!IS_AJAX)
		{
			$this->error(L('common_unauthorized_access'));
		}

		// 获取数据
		$field = array('id', 'pid', 'icon', 'name', 'sort', 'is_enable');
		$data = M('GoodsCategory')->field($field)->where(array('pid'=>intval(I('id', 0))))->select();
		if(!empty($data))
		{
			$image_host = C('IMAGE_HOST');
			foreach($data as &$v)
			{
				$v['is_son']		=	$this->IsExistSon($v['id']);
				$v['ajax_url']		=	U('Admin/GoodsCategory/GetNodeSon', array('id'=>$v['id']));
				$v['delete_url']	=	U('Admin/GoodsCategory/Delete');
				$v['icon_url']		=	empty($v['icon']) ? '' : $image_host.$v['icon'];
				$v['json']			=	json_encode($v);
			}
		}
		$msg = empty($data) ? L('common_not_data_tips') : L('common_operation_success');
		$this->ajaxReturn($msg, 0, $data);
	}

	/**
	 * [IsExistSon 节点是否存在子数据]
	 * @author   Devil
	 * @blog     http://gong.gg/
	 * @version  0.0.1
	 * @datetime 2016-12-25T15:22:47+0800
	 * @param    [int]    $id [节点id]
	 * @return   [string]     [有数据ok, 则no]
	 */
	private function IsExistSon($id)
	{
		if(!empty($id))
		{
			return (M('GoodsCategory')->where(array('pid'=>$id))->count() > 0) ? 'ok' : 'no';
		}
		return 'no';
	}

	/**
	 * [Save 分类保存]
	 * @author   Devil
	 * @blog     http://gong.gg/
	 * @version  0.0.1
	 * @datetime 2016-12-25T22:36:12+0800
	 */
	public function Save()
	{
		// 是否ajax请求
		if(!IS_AJAX)
		{
			$this->error(L('common_unauthorized_access'));
		}

		// 图片
        if(!empty($_FILES['file_icon']))
        {
            // 文件上传校验
            $error = FileUploadError('file_icon');
            if($error !== true)
            {
                $this->ajaxReturn($error, -1);
            }

            // 文件类型
            list($type, $suffix) = explode('/', $_FILES['file_icon']['type']);
            $path = 'Public'.DS.'Upload'.DS.'category'.DS.date('Y').DS.date('m').DS;
            if(!is_dir($path))
            {
                mkdir(ROOT_PATH.$path, 0777, true);
            }
            $filename = date('YmdHis').GetNumberCode(6).'.'.$suffix;
            $file_icon = $path.$filename;

            if(move_uploaded_file($_FILES['file_icon']['tmp_name'], ROOT_PATH.$file_icon))
            {
                $_POST['icon'] = DS.$file_icon;
            }
        }

		// id为空则表示是新增
		$m = D('GoodsCategory');

		// 公共额外数据处理
		$m->sort 	=	intval(I('sort'));

		// 添加
		if(empty($_POST['id']))
		{
			if($m->create($_POST, 1))
			{
				// 额外数据处理
				$m->add_time	=	time();
				$m->name 		=	I('name');
				
				// 写入数据库
				if($m->add())
				{
					$this->ajaxReturn(L('common_operation_add_success'));
				} else {
					$this->ajaxReturn(L('common_operation_add_error'), -100);
				}
			}
		} else {
			// 编辑
			if($m->create($_POST, 2))
			{
				// 额外数据处理
				$m->name 		=	I('name');
				$m->upd_time	=	time();

				// 移除 id
				unset($m->id, $m->pid);

				// 更新数据库
				if($m->where(array('id'=>I('id')))->save())
				{
					$this->ajaxReturn(L('common_operation_edit_success'));
				} else {
					$this->ajaxReturn(L('common_operation_edit_error'), -100);
				}
			}
		}
		$this->ajaxReturn($m->getError(), -1);
	}

	/**
	 * [Delete 分类删除]
	 * @author   Devil
	 * @blog     http://gong.gg/
	 * @version  0.0.1
	 * @datetime 2016-12-25T22:36:12+0800
	 */
	public function Delete()
	{
		if(!IS_AJAX)
		{
			$this->error(L('common_unauthorized_access'));
		}

		$m = D('GoodsCategory');
		if($m->create($_POST, 5))
		{
			if($m->delete(I('id')))
			{
				$this->ajaxReturn(L('common_operation_delete_success'));
			} else {
				$this->ajaxReturn(L('common_operation_delete_error'), -100);
			}
		} else {
			$this->ajaxReturn($m->getError(), -1);
		}
	}
}
?>