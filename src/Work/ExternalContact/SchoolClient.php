<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\ExternalContact;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author MillsGuo <millsguo@gmail.com>
 */
class SchoolClient extends BaseClient
{
    /**
     * 创建部门
     * @see https://work.weixin.qq.com/api/doc/90000/90135/92340
     * @param string $name
     * @param int $parentId
     * @param int $type
     * @param int $standardGrade
     * @param int $registerYear
     * @param int $order
     * @param array $departmentAdmins [['userid':'139','type':1],['userid':'1399','type':2]]
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function createDepartment(string $name, int $parentId, int $type, int $standardGrade, int $registerYear, int $order, array $departmentAdmins)
    {
        $params = [
            'name' => $name,
            'parentid' => $parentId,
            'type' => $type,
            'standard_grade' => $standardGrade,
            'register_year' => $registerYear,
            'order' => $order,
            'department_admins' => $departmentAdmins
        ];

        return $this->httpPostJson('cgi-bin/school/department/create', $params);
    }

    /**
     * 更新部门
     * @see https://work.weixin.qq.com/api/doc/90000/90135/92341
     * @param int $id
     * @param string $name
     * @param int $parentId
     * @param int $type
     * @param int $standardGrade
     * @param int $registerYear
     * @param int $order
     * @param array $departmentAdmins [['op':0,'userid':'139','type':1],['op':1,'userid':'1399','type':2]] OP=0表示新增或更新，OP=1表示删除管理员
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function updateDepartment(int $id, string $name, int $parentId, int $type, int $standardGrade, int $registerYear, int $order, array $departmentAdmins)
    {
        $params = [
            'id' => $id,
            'name' => $name,
            'parentid' => $parentId,
            'type' => $type,
            'standard_grade' => $standardGrade,
            'register_year' => $registerYear,
            'order' => $order,
            'department_admins' => $departmentAdmins
        ];
        $params = $this->filterNullValue($params);

        return $this->httpPostJson('cgi-bin/school/department/update', $params);
    }

    /**
     * 删除部门
     * @see https://work.weixin.qq.com/api/doc/90000/90135/92342
     * @param int $id
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function deleteDepartment(int $id)
    {
        return $this->httpGet('cgi-bin/school/department/delete', [
            'id' => $id
        ]);
    }

    /**
     * 获取部门列表
     * @see https://work.weixin.qq.com/api/doc/90000/90135/92343
     * @param int $id 如果ID为0，则获取全量组织架构
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getDepartments(int $id)
    {
        if ($id > 0) {
            $params = [
                'id' => $id
            ];
        } else {
            $params = [];
        }

        return $this->httpGet('cgi-bin/school/department/list', $params);
    }

    /**
     * 创建学生
     * @see https://work.weixin.qq.com/api/doc/90000/90135/92325
     * @param string $userId
     * @param string $name
     * @param array $department
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function createStudent(string $userId, string $name, array $department)
    {
        $params = [
            'student_userid' => $userId,
            'name' => $name,
            'department' => $department
        ];

        return $this->httpPostJson('cgi-bin/school/user/create_student', $params);
    }

    /**
     * 删除学生
     * @see https://work.weixin.qq.com/api/doc/90000/90135/92326
     * @param string $userId
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function deleteStudent(string $userId)
    {
        return $this->httpGet('cgi-bin/school/user/delete_student', [
            'userid' => $userId
        ]);
    }

    /**
     * 更新学生
     * @see  https://work.weixin.qq.com/api/doc/90000/90135/92327
     * @param string $userId
     * @param string $name
     * @param string $newUserId
     * @param array $department
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function updateStudent(string $userId, string $name, string $newUserId, array $department)
    {
        $params = [
            'student_userid' => $userId
        ];
        if (!empty($name)) {
            $params['name'] = $name;
        }
        if (!empty($newUserId)) {
            $params['new_student_userid'] = $newUserId;
        }
        if (!empty($department)) {
            $params['department'] = $department;
        }

        return $this->httpPostJson('cgi-bin/school/user/update_student', $params);
    }

    /**
     * 批量创建学生，学生最多100个
     * @see https://work.weixin.qq.com/api/doc/90000/90135/92328
     * @param array $students 学生格式：[[student_userid:'','name':'','department':[1,2]],['student_userid':'','name':'','department':[1,2]]]
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function batchCreateStudents(array $students)
    {
        $params = [
            'students' => $students
        ];

        return $this->httpPostJson('cgi-bin/school/user/batch_create_student', $params);
    }

    /**
     * 批量删除学生，每次最多100个学生
     * @see https://work.weixin.qq.com/api/doc/90000/90135/92329
     * @param array $useridList 学生USERID，格式：['zhangsan','lisi']
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function batchDeleteStudents(array $useridList)
    {
        return $this->httpPostJson('cgi-bin/school/user/batch_delete_student', [
            'useridlist' => $useridList
        ]);
    }

    /**
     * 批量更新学生，每次最多100个
     * @see https://open.work.weixin.qq.com/api/doc/90001/90143/92042
     * @param array $students 格式：[['student_userid':'lisi','new_student_userid':'lisi2','name':'','department':[1,2]],.....]
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function batchUpdateStudents(array $students)
    {
        return $this->httpPostJson('cgi-bin/school/user/batch_update_student', [
            'students' => $students
        ]);
    }

    /**
     * 创建家长
     * @see https://open.work.weixin.qq.com/api/doc/90001/90143/92077
     * @param string $userId
     * @param string $mobile
     * @param bool $toInvite
     * @param array $children
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function createParent(string $userId, string $mobile, bool $toInvite, array $children)
    {
        $params = [
            'parent_userid' => $userId,
            'mobile' => $mobile,
            'to_invite' => $toInvite,
            'children' => $children
        ];

        return $this->httpPostJson('cgi-bin/school/user/create_parent', $params);
    }

    /**
     * 删除家长
     * @see https://open.work.weixin.qq.com/api/doc/90001/90143/92079
     * @param string $userId
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function deleteParent(string $userId)
    {
        return $this->httpPostJson('cgi-bin/school/user/delete_parent', [
            'userid' => $userId
        ]);
    }

    /**
     * 更新家长
     * @see https://open.work.weixin.qq.com/api/doc/90001/90143/92081
     * @param string $userId
     * @param string $mobile
     * @param string $newUserId
     * @param array $children 格式：[['student_userid':'','relation':''],[]]
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function updateParent(string $userId, string $mobile, string $newUserId, array $children)
    {
        $params = [
            'parent_userid' => $userId
        ];
        if (!empty($newUserId)) {
            $params['new_parent_userid'] = $newUserId;
        }
        if (!empty($mobile)) {
            $params['mobile'] = $mobile;
        }
        if (!empty($children)) {
            $params['children'] = $children;
        }

        return $this->httpPostJson('cgi-bin/school/user/update_parent', $params);
    }

    /**
     * 批量创建家长 每次最多100个
     * @see https://open.work.weixin.qq.com/api/doc/90001/90143/92078
     * @param array $parents [['parent_userid':'','mobile':'','to_invite':true,'children':['student_userid':'','relation':'']],.....]
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function batchCreateParents(array $parents)
    {
        return $this->httpPostJson('cgi-bin/school/user/batch_create_parent', [
            'parents' => $parents
        ]);
    }

    /**
     * 批量删除家长，每次最多100个
     * @see https://open.work.weixin.qq.com/api/doc/90001/90143/92080
     * @param array $userIdList 格式：['chang','lisi']
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function batchDeleteParents(array $userIdList)
    {
        return $this->httpPostJson('cgi-bin/school/user/batch_delete_parent', [
            'useridlist' => $userIdList
        ]);
    }

    /**
     * 批量更新家长，每次最多100个
     * @see https://open.work.weixin.qq.com/api/doc/90001/90143/92082
     * @param array $parents 格式：[['parent_userid':'','new_parent_userid':'','mobile':'','children':[['student_userid':'','relation':''],...]],.....]
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function batchUpdateParents(array $parents)
    {
        return $this->httpPostJson('cgi-bin/school/user/batch_update_parent', [
            'parents' => $parents
        ]);
    }

    /**
     * 读取学生或家长
     * @see https://open.work.weixin.qq.com/api/doc/90001/90143/92038
     * @param string $userId 学生或家长的userid
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getUser(string $userId)
    {
        return $this->httpGet('cgi-bin/school/user/get', [
            'userid' => $userId
        ]);
    }

    /**
     * 获取部门成员详情
     * @see https://open.work.weixin.qq.com/api/doc/90001/90143/92038
     * @param int $departmentId
     * @param bool $fetchChild
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getStudents(int $departmentId, bool $fetchChild)
    {
        $params = [
            'department_id' => $departmentId
        ];
        if ($fetchChild) {
            $params['fetch_child'] = 1;
        } else {
            $params['fetch_child'] = 0;
        }

        return $this->httpGet('cgi-bin/school/user/list', $params);
    }

    /**
     * 获取学校通知二维码
     * @see https://open.work.weixin.qq.com/api/doc/90001/90143/92197
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getSubscribeQrCode()
    {
        return $this->httpGet('cgi-bin/externalcontact/get_subscribe_qr_code');
    }

    /**
     * 设置关注学校通知的模式
     * @see https://open.work.weixin.qq.com/api/doc/90001/90143/92290
     * @param int $mode 关注模式，1可扫码填写资料加入，2禁止扫码填写资料加入
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function setSubscribeMode(int $mode)
    {
        return $this->httpPostJson('cgi-bin/externalcontact/set_subscribe_mode', [
            'subscribe_mode' => $mode
        ]);
    }

    /**
     * 获取关注学校通知的模式
     * @see https://open.work.weixin.qq.com/api/doc/90001/90143/92290
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getSubscribeMode()
    {
        return $this->httpGet('cgi-bin/externalcontact/get_subscribe_mode');
    }

    /**
     * 设置【老师可查看班级】的模式
     * @see https://open.work.weixin.qq.com/api/doc/90001/90143/92652
     * @param int $mode
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function setTeacherViewMode(int $mode)
    {
        return $this->httpPostJson('cgi-bin/school/set_teacher_view_mode', [
            'view_mode' => $mode
        ]);
    }

    /**
     * 获取【老师可查看班级】的模式
     * @see https://open.work.weixin.qq.com/api/doc/90001/90143/92652
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getTeacherViewMode()
    {
        return $this->httpGet('cgi-bin/school/get_teacher_view_mode');
    }

    /**
     * 外部联系人OPENID转换
     * @param string $userId
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function convertOpenid(string $userId)
    {
        return $this->httpGet('cgi-bin/externalcontact/convert_to_openid', [
            'external_userid' => $userId
        ]);
    }

    /**
     * 过滤数组中值为NULL的键
     * @param array $data
     * @return array
     */
    protected function filterNullValue(array $data)
    {
        $returnData = [];
        foreach ($data as $key => $value) {
            if ($value !== null) {
                $returnData[$key] = trim($value);
            }
        }

        return $returnData;
    }
}
