<?php

declare (strict_types = 1);

/**
 * This file is part of CodeIgniter Shield.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Config;

use CodeIgniter\Shield\Config\AuthGroups as ShieldAuthGroups;

class AuthGroups extends ShieldAuthGroups
{
    /**
     * --------------------------------------------------------------------
     * Default Group
     * --------------------------------------------------------------------
     * The group that a newly registered user is added to.
     */
    public string $defaultGroup = 'user';

    /**
     * --------------------------------------------------------------------
     * Groups
     * --------------------------------------------------------------------
     * An associative array of the available groups in the system, where the keys
     * are the group names and the values are arrays of the group info.
     *
     * Whatever value you assign as the key will be used to refer to the group
     * when using functions such as:
     *      $user->addGroup('superadmin');
     *
     * @var array<string, array<string, string>>
     *
     * @see https://codeigniter4.github.io/shield/quick_start_guide/using_authorization/#change-available-groups for more info
     */
    public array $groups = [
        'superadmin' => [
            'title'       => 'Super Admin',
            'description' => 'Complete control of the site.',
        ],
        'admin'      => [
            'title'       => 'Admin',
            'description' => 'Day to day administrators of the site.',
        ],
        'rtm-admin'  => [
            'title'       => 'RTMD Admin',
            'description' => 'Day to day administrators of the RTMD activitis.',
        ],
        'rtm-user'   => [
            'title'       => 'RTMD User',
            'description' => 'General users of the RTMD activities.',
        ],
        'user'       => [
            'title'       => 'User',
            'description' => 'General users of the site. Often customers.',
        ],
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions
     * --------------------------------------------------------------------
     * The available permissions in the system.
     *
     * If a permission is not listed here it cannot be used.
     */
    public array $permissions = [
        'admin.access'                    => 'Can access the sites admin area',
        'admin.settings'                  => 'Can access the main site settings',
        'users.manage-admins'             => 'Can manage other admins',
        'users.create'                    => 'Can create new non-admin users',
        'users.edit'                      => 'Can edit existing non-admin users',
        'users.delete'                    => 'Can delete existing non-admin users',

        //Part-I Data
        'partone.list'                    => 'Can list all FCPS Part-I candidates',

        //Training permissions
        'training.trainee.list'           => 'Can list all trainees',
        'training.basic.get'              => 'Can access the basic info of trainee',
        'training.basic.edit'             => 'Can edit basic information',
        'training.create'                 => 'Can create training data',
        'training.edit'                   => 'Can edit training data',
        'training.delete'                 => 'Can delete training data',

        //Trainee permissions
        'trainee.basic.info'              => 'Can access own basic information',
        'trainee.basic.info.update'       => 'Can update own basic information',
        'trainee.progress.reports.create' => 'Can create own progress reports',
        'trainee.training.application'    => 'Can create training application',
        'trainee.honorarium.application'  => 'Can create honorarium bill application',

        //Honorarium application permissions
        'applications.index'              => 'Can see the list all honorarium applications',
        'applications.approve'            => 'Can approve training applications',
        'applications.reject'             => 'Can reject training applications',
        'applications.edit'               => 'Can edit training applications',
        'applications.basic.update'       => 'Can update basic info of training applications',
        'applications.fcps.update'        => 'Can update FCPS info of training applications',
        'applications.mbbs.update'        => 'Can update MBBS info of training applications',
        'applications.bank.update'        => 'Can update bank info of training applications',

        //Honorarium permissions
        'bills.index'                     => 'Can see the list all honorarium bills',
        'bills.approve'                   => 'Can approve honorarium bills',
        'bills.reject'                    => 'Can reject honorarium bills',
        'bills.edit'                      => 'Can edit honorarium bills',
        'bills.update'                    => 'Can update honorarium bills',
        'bills.training.edit'             => 'Can edit honorarium bill training info',
        'bills.training.update'           => 'Can update honorarium bill training info',
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions Matrix
     * --------------------------------------------------------------------
     * Maps permissions to groups.
     *
     * This defines group-level permissions.
     */
    public array $matrix = [
        'superadmin' => [
            'admin.*',
            'users.*',
            'partone.*',
            'training.*',
            'applications.*',
            'bills.*',
            'trainee.*',
            'reports.*',
        ],
        'admin'      => [
            'admin.access',
            'users.create',
            'users.edit',
            'users.delete',

            'partone.list',
            'reports.*',
        ],
        'rtm-admin'  => [
            //Part-I Data
            'partone.list',
            'partone.candidate.show',
            'partone.candidate.edit',

            //Applications permissions
            'applications.index',
            'applications.approve',
            'applications.reject',
            'applications.edit',
            'applications.basic.update',
            'applications.fcps.update',
            'applications.mbbs.update',
            'applications.bank.update',

            //Honorarium Bill permissions
            'bills.index',
            'bills.approve',
            'bills.reject',
            'bills.edit',
            'bills.update',
            'bills.training.edit',
            'bills.training.update',
        ],
        'rtm-user'   => [
            'applications.index',
            'applications.approve',
            'applications.reject',
            'applications.edit',
            'applications.basic.update',
            'applications.fcps.update',
            'applications.mbbs.update',
            'applications.bank.update',

            'bills.index',
            'bills.approve',
            'bills.reject',
            'bills.edit',
            'bills.update',
            'bills.training.edit',
            'bills.training.update',
        ],
        'user'       => [
            'trainee.basic.info',
            'trainee.basic.info.update',
            'trainee.progress.reports.create',
            'trainee.training.application',
            'trainee.honorarium.application',
        ],
    ];
}
