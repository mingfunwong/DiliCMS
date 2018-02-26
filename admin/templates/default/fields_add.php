<?php if ( ! defined('IN_CMS')) exit('No direct script access allowed');?>
<div class="headbar">
    <div class="position"><?=$bread?></div>
</div>
<div class="content_box">
    <div class="content form_content">
        <?php echo form_open($this->uri->rsegment(1).'/add_field/'.$model->id, 'method="post" target="_blank"'); ?>
            <table class="form_table dili_tabs" id="site_basic" >
                <col width="150px" />
                <col />
                <tr>
                    <th> 字段标识：</th>
                    <td><?php $this->form->show('name','input',''); ?><label>*字段名称，2-20位字母。</label><?php echo form_error('name'); ?></td>
                </tr>
                <tr>
                    <th> 字段名称：</th>
                    <td><?php $this->form->show('description','input',''); ?><label>*有意义的名称,最多40个字符。</label><?php echo form_error('description'); ?></td>
                </tr>
                <tr>
                    <th> 字段类型：</th>
                    <td><?php $this->form->show('type','select',array_merge(setting('fieldtypes'),setting('extra_fieldtypes'))); ?><label>*选择一个适当的字段类型。</label><?php echo form_error('type'); ?></td>
                </tr>
                <tr>
                    <th> 字段长度：</th>
                    <td><?php $this->form->show('length','input',''); ?><label>设置一个适当的字段长度,可以不填写，参看默认值.</label><?php echo form_error('length'); ?></td>
                </tr>
                
                <tr>
                    <th> 数据源：</th>
                    <td><?php $this->form->show('values','input',''); ?><label>可以为某些字段类型提供数据源或者默认值，使用方式见手册。</label><?php echo form_error('values'); ?></td>
                </tr>
                <tr>
                    <th> 显示尺寸：</th>
                    <td>
                        宽：<?php $this->form->show('width','input',''); ?><label>*表单控件的显示的宽度,单位为px</label><?php echo form_error('width'); ?><br  />
                        高：<?php $this->form->show('height','input',''); ?><label>*表单控件的显示的高度,单位为px</label><?php echo form_error('height'); ?>
                    </td>
                </tr>
                <tr>
                    <th> 验证规则：</th>
                    <td><?php $this->form->show('rules','checkbox',setting('validation')); ?><label></label><?php echo form_error('rules'); ?></td>
                </tr>
                <tr>
                    <th> 规则说明：</th>
                    <td><?php $this->form->show('ruledescription','input',''); ?><label></label><?php echo form_error('ruledescription'); ?></td>
                </tr>
                <tr>
                    <th> 管理选项：</th>
                    <td>
                        <?php $this->form->show('searchable','checkbox','是否加入搜索', '1');?>
                        <?php $this->form->show('listable','checkbox','是否列表显示', '1'); ?>
                        <?php $this->form->show('editable','checkbox','是否允许编辑', '1'); ?>
                    </td>
                </tr>
                <tr>
                    <th> 显示顺序：</th>
                    <td><?php $this->form->show('order','input',''); ?><label></label><?php echo form_error('order'); ?></td>
                </tr>
                <tr>
                    <th></th>
                    <td>
                        <button class="submit" type='submit'><span>添加新字段</span></button>
                    </td>
                </tr>
                <tr>
                    <th></th>
                    <td>
                        <div class="red_box">友情提示:1个模型中必须要有1个字段设置1个验证规则，否则无法添加数据。</div>
                    </td>
                </tr>
            </table>
        <?php echo form_close(); ?>
    </div>
    <div class="fast-add">
        <h3>快速添加</h3>
        <ul>
            <li><a href="javascript:;" data-data="name 名称 input 100    1   1 1 1 10">名称</a></li>
            <li><a href="javascript:;" data-data="intro 简介 textarea 500       1 1 1 20">简介</a></li>
            <li><a href="javascript:;" data-data="content 内容 wysiwyg 5000       1  1 30">内容</a></li>
            <li><a href="javascript:;" data-data="image 上传图片 file 200         1 40">上传图片</a></li>
            <li><a href="javascript:;" data-data="link 超链接 input 200       1 1 1 50">超链接</a></li>
            <li><a href="javascript:;" data-data="type 类型 checkbox 10       1 1 1 60">类型</a></li>
            <li><a href="javascript:;" data-data="class 分类 radio_from_model 10       1 1 1 70">分类</a></li>
            <li><a href="javascript:;" data-data="order 排序 int 10 100     顺序，按数字从小到大排序 1  1 100">排序</a></li>
            <li><a href="javascript:;" data-data="keywords 页面关键字 input 500         1 110">页面关键字</a></li>
            <li><a href="javascript:;" data-data="description 页面描述 input 500         1 120">页面描述</a></li>
            <li><a href="javascript:;" data-data="click_count 点击次数 int 10 0      1 1  130">点击次数</a></li>
            <li><a href="javascript:;" data-data="datetime 添加时间 datetime 50       1 1 1 140">添加时间</a></li>
        </ul>
    </div>
</div>

<style>
.content_box{position: relative;}
.fast-add{position: absolute;left: 850px;top: 0;font-size: 16px;}
.fast-add li{line-height: 2em;}
.fast-add a{padding: 0 20px; display: block;}
</style>
<script>
$(".fast-add a").on("click", function (){
    var data = $(this).data('data').split(" ");
    if (data[0] == 'datetime') {
        data.splice(4,1,'yyyy-MM-dd HH:mm:ss');
    }
    for(var i=0; i<data.length; i++)
    {
        $(".form_content input, .form_content select").eq(i).val(data[i]).attr("checked", (data[i]));
        if (data[i]) {
            $(".form_content input, .form_content select").eq(i).attr("checked", true);
        } else {
            $(".form_content input, .form_content select").eq(i).attr("checked", false);
        }
    }

});
</script>

