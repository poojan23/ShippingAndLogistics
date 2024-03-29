<?php

class ControllerCommonNav extends PT_Controller
{
    public function index()
    {
        if (isset($this->request->get['user_token']) && isset($this->session->data['user_token']) && ((string)$this->request->get['user_token'] == $this->session->data['user_token'])) {
            $this->load->language('common/nav');

            # Create a 3 level menu array
            # Level 2 can not have children

            # Menu
            $data['menus'][] = array(
                'id'        => 'menu-dashboard',
                'icon'      => 'fa-tachometer-alt',
                'name'      => $this->language->get('text_dashboard'),
                'href'      => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token']),
                'children'  => array()
            );

            # Catalog
            $catalog = array();

            if ($this->user->hasPermission('access', 'catalog/information')) {
                $catalog[] = array(
                    'name'      => $this->language->get('text_information'),
                    'href'      => $this->url->link('catalog/information', 'user_token=' . $this->session->data['user_token']),
                    'children'  => array()
                );
            }
            
            if ($this->user->hasPermission('access', 'catalog/information_group')) {
                $catalog[] = array(
                    'name'      => $this->language->get('text_information_group'),
                    'href'      => $this->url->link('catalog/information_group', 'user_token=' . $this->session->data['user_token']),
                    'children'  => array()
                );
            }
            
            if ($this->user->hasPermission('access', 'catalog/download')) {
                $catalog[] = array(
                    'name'      => $this->language->get('text_download'),
                    'href'      => $this->url->link('catalog/download', 'user_token=' . $this->session->data['user_token']),
                    'children'  => array()
                );
            }
            
            if ($this->user->hasPermission('access', 'catalog/area')) {
                $catalog[] = array(
                    'name'      => $this->language->get('text_area'),
                    'href'      => $this->url->link('catalog/area', 'user_token=' . $this->session->data['user_token']),
                    'children'  => array()
                );
            }

            if ($catalog) {
                $data['menus'][] = array(
                    'id'        => 'menu-catalog',
                    'icon'      => 'fa-tags',
                    'name'      => $this->language->get('text_catalog'),
                    'href'      => '',
                    'children'  => $catalog
                );
            }

            # Customer
            $customer = array();
            
            if ($this->user->hasPermission('access', 'customer/customer')) {
                $customer[] = array(
                    'name'      => $this->language->get('text_customer'),
                    'href'      => $this->url->link('customer/customer', 'user_token=' . $this->session->data['user_token']),
                    'children'  => array()
                );
            }
            
            if ($this->user->hasPermission('access', 'customer/customer_group')) {
                $customer[] = array(
                    'name'      => $this->language->get('text_customer_group'),
                    'href'      => $this->url->link('customer/customer_group', 'user_token=' . $this->session->data['user_token']),
                    'children'  => array()
                );
            }
            
                        
            if ($this->user->hasPermission('access', 'report/custom_report')) {
                $customer[] = array(
                    'name'      => $this->language->get('text_custom_report'),
                    'href'      => $this->url->link('report/custom_report', 'user_token=' . $this->session->data['user_token']),
                    'children'  => array()
                );
            }
   
            if ($customer) {
                $data['menus'][] = array(
                    'id'        => 'menu-design',
                    'icon'      => 'fa-users',
                    'name'      => $this->language->get('text_customer'),
                    'href'      => '',
                    'children'  => $customer
                );
            }
            
            # Report
            $report = array();
            
            if ($this->user->hasPermission('access', 'report/import_report')) {
                $report[] = array(
                    'name'      => $this->language->get('text_import_report'),
                    'href'      => $this->url->link('report/import_report', 'user_token=' . $this->session->data['user_token']),
                    'children'  => array()
                );
            }
//            
//            if ($this->user->hasPermission('access', 'report/manual_report')) {
//                $report[] = array(
//                    'name'      => $this->language->get('text_manual_report'),
//                    'href'      => $this->url->link('report/manual_report', 'user_token=' . $this->session->data['user_token']),
//                    'children'  => array()
//                );
//            }
    
            if ($report) {
                $data['menus'][] = array(
                    'id'        => 'menu-design',
                    'icon'      => 'fa-file',
                    'name'      => $this->language->get('text_import_report'),
                    'href'      => '',
                    'children'  => $report
                );
            }
            
            
//            # Event
//            $event = array();
//            
//            if ($this->user->hasPermission('access', 'catalog/event')) {
//                $event[] = array(
//                    'name'      => $this->language->get('text_event'),
//                    'href'      => $this->url->link('catalog/event', 'user_token=' . $this->session->data['user_token']),
//                    'children'  => array()
//                );
//            }
//            if ($this->user->hasPermission('access', 'catalog/event_group')) {
//                $event[] = array(
//                    'name'      => $this->language->get('text_event_group'),
//                    'href'      => $this->url->link('catalog/event_group', 'user_token=' . $this->session->data['user_token']),
//                    'children'  => array()
//                );
//            }
//   
//            if ($event) {
//                $data['menus'][] = array(
//                    'id'        => 'menu-design',
//                    'icon'      => 'fa-desktop',
//                    'name'      => $this->language->get('text_event'),
//                    'href'      => '',
//                    'children'  => $event
//                );
//            }
//            
//            # Gallery
//            $gallery = array();
//            
//            if ($this->user->hasPermission('access', 'gallery/gallery')) {
//                $gallery[] = array(
//                    'name'      => $this->language->get('text_gallery'),
//                    'href'      => $this->url->link('gallery/gallery', 'user_token=' . $this->session->data['user_token']),
//                    'children'  => array()
//                );
//            }
//            
//            if ($this->user->hasPermission('access', 'gallery/gallery_group')) {
//                $gallery[] = array(
//                    'name'      => $this->language->get('text_gallery_group'),
//                    'href'      => $this->url->link('gallery/gallery_group', 'user_token=' . $this->session->data['user_token']),
//                    'children'  => array()
//                );
//            }
//            
//             if ($gallery) {
//                $data['menus'][] = array(
//                    'id'        => 'menu-design',
//                    'icon'      => 'fa-desktop',
//                    'name'      => $this->language->get('text_gallery'),
//                    'href'      => '',
//                    'children'  => $gallery
//                );
//            }
//            
//            # Sport
//            $sport = array();
//            
//            if ($this->user->hasPermission('access', 'sport/sport')) {
//                $sport[] = array(
//                    'name'      => $this->language->get('text_sport'),
//                    'href'      => $this->url->link('sport/sport', 'user_token=' . $this->session->data['user_token']),
//                    'children'  => array()
//                );
//            }
//            
//            if ($this->user->hasPermission('access', 'sport/sport_group')) {
//                $sport[] = array(
//                    'name'      => $this->language->get('text_sport_group'),
//                    'href'      => $this->url->link('sport/sport_group', 'user_token=' . $this->session->data['user_token']),
//                    'children'  => array()
//                );
//            }
//   
//            if ($sport) {
//                $data['menus'][] = array(
//                    'id'        => 'menu-design',
//                    'icon'      => 'fa-desktop',
//                    'name'      => $this->language->get('text_sport'),
//                    'href'      => '',
//                    'children'  => $sport
//                );
//            }
//
//            # Venue
//            $venue = array();
//            
//            if ($this->user->hasPermission('access', 'venue/venue')) {
//                $venue[] = array(
//                    'name'      => $this->language->get('text_venue'),
//                    'href'      => $this->url->link('venue/venue', 'user_token=' . $this->session->data['user_token']),
//                    'children'  => array()
//                );
//            }
//            
//            if ($this->user->hasPermission('access', 'venue/venue_group')) {
//                $venue[] = array(
//                    'name'      => $this->language->get('text_venue_group'),
//                    'href'      => $this->url->link('venue/venue_group', 'user_token=' . $this->session->data['user_token']),
//                    'children'  => array()
//                );
//            }
//   
//            if ($venue) {
//                $data['menus'][] = array(
//                    'id'        => 'menu-design',
//                    'icon'      => 'fa-desktop',
//                    'name'      => $this->language->get('text_venue'),
//                    'href'      => '',
//                    'children'  => $venue
//                );
//            }
            
            # Design
            $design = array();
            
            if ($this->user->hasPermission('access', 'design/banner')) {
                $design[] = array(
                    'name'      => $this->language->get('text_banner'),
                    'href'      => $this->url->link('design/banner', 'user_token=' . $this->session->data['user_token']),
                    'children'  => array()
                );
            }
            
            if ($this->user->hasPermission('access', 'design/translation')) {
                $design[] = array(
                    'name'      => $this->language->get('text_language_editor'),
                    'href'      => $this->url->link('design/translation', 'user_token=' . $this->session->data['user_token']),
                    'children'  => array()
                );
            }

            if ($design) {
                $data['menus'][] = array(
                    'id'        => 'menu-design',
                    'icon'      => 'fa-desktop',
                    'name'      => $this->language->get('text_design'),
                    'href'      => '',
                    'children'  => $design
                );
            }

            # Enquiry
            if ($this->user->hasPermission('access', 'common/enquiry')) {
                $data['menus'][] = array(
                    'id'        => 'menu-enquiry',
                    'icon'      => 'fa-envelope',
                    'name'      => $this->language->get('text_enquiry'),
                    'href'      => $this->url->link('common/enquiry', 'user_token=' . $this->session->data['user_token']),
                    'children'  => array()
                );
            }

            # Testimonial
            if ($this->user->hasPermission('access', 'common/testimonial')) {
                $data['menus'][] = array(
                    'id'        => 'menu-testimonial',
                    'icon'      => 'fa-pen-alt',
                    'name'      => $this->language->get('text_testimonial'),
                    'href'      => $this->url->link('common/testimonial', 'user_token=' . $this->session->data['user_token']),
                    'children'  => array()
                );
            }
            
            # Settings
            # System
            $system = array();

            if ($this->user->hasPermission('access', 'setting/store')) {
                $system[] = array(
                    'name'      => $this->language->get('text_store'),
                    'href'      => $this->url->link('setting/store', 'user_token=' . $this->session->data['user_token']),
                    'children'  => array()
                );
            }

            # Users
            $user = array();

            if ($this->user->hasPermission('access', 'user/user')) {
                $user[] = array(
                    'name'      => $this->language->get('text_user'),
                    'href'      => $this->url->link('user/user', 'user_token=' . $this->session->data['user_token']),
                    'children'  => array()
                );
            }

            if ($this->user->hasPermission('access', 'user/user_permission')) {
                $user[] = array(
                    'name'      => $this->language->get('text_user_group'),
                    'href'      => $this->url->link('user/user_permission', 'user_token=' . $this->session->data['user_token']),
                    'children'  => array()
                );
            }

            if ($user) {
                $system[] = array(
                    'name'      => $this->language->get('text_user'),
                    'href'      => '',
                    'children'  => $user
                );
            }

            # Localisation
            $localisation = array();

//            if ($this->user->hasPermission('access', 'localisation/language')) {
//                $localisation[] = array(
//                    'name'      => $this->language->get('text_language'),
//                    'href'      => $this->url->link('localisation/language', 'user_token=' . $this->session->data['user_token']),
//                    'children'  => array()
//                );
//            }

            if ($this->user->hasPermission('access', 'localisation/country')) {
                $localisation[] = array(
                    'name'      => $this->language->get('text_country'),
                    'href'      => $this->url->link('localisation/country', 'user_token=' . $this->session->data['user_token']),
                    'children'  => array()
                );
            }

            if ($this->user->hasPermission('access', 'localisation/zone')) {
                $localisation[] = array(
                    'name'      => $this->language->get('text_zone'),
                    'href'      => $this->url->link('localisation/zone', 'user_token=' . $this->session->data['user_token']),
                    'children'  => array()
                );
            }

            if ($localisation) {
                $system[] = array(
                    'name'      => $this->language->get('text_localisation'),
                    'href'      => '',
                    'children'  => $localisation
                );
            }

            # Tools
            $maintenance = array();

            if ($this->user->hasPermission('access', 'tool/upgrade')) {
                $maintenance[] = array(
                    'name'      => $this->language->get('text_upgrade'),
                    'href'      => $this->url->link('tool/upgrade', 'user_token=' . $this->session->data['user_token']),
                    'children'  => array()
                );
            }

            if ($this->user->hasPermission('access', 'tool/backup')) {
                $maintenance[] = array(
                    'name'      => $this->language->get('text_backup'),
                    'href'      => $this->url->link('tool/backup', 'user_token=' . $this->session->data['user_token']),
                    'children'  => array()
                );
            }

            if ($this->user->hasPermission('access', 'tool/upload')) {
                $maintenance[] = array(
                    'name'      => $this->language->get('text_upload'),
                    'href'      => $this->url->link('tool/upload', 'user_token=' . $this->session->data['user_token']),
                    'children'  => array()
                );
            }

            if ($this->user->hasPermission('access', 'tool/log')) {
                $maintenance[] = array(
                    'name'      => $this->language->get('text_log'),
                    'href'      => $this->url->link('tool/log', 'user_token=' . $this->session->data['user_token']),
                    'children'  => array()
                );
            }

            if ($maintenance) {
                $system[] = array(
                    'name'      => $this->language->get('text_maintenance'),
                    'href'      => '',
                    'children'  => $maintenance
                );
            }

            if ($system) {
                $data['settings'][] = array(
                    'id'        => 'menu-system',
                    'icon'      => 'fa-cog',
                    'name'      => $this->language->get('text_system'),
                    'href'      => '',
                    'children'  => $system
                );
            }

            # Reports
            $report = array();

            if ($this->user->hasPermission('access', 'report/report')) {
                $report[] = array(
                    'name'      => $this->language->get('text_reports'),
                    'href'      => $this->url->link('report/report', 'user_token=' . $this->session->data['user_token']),
                    'children'  => array()
                );
            }

            if ($this->user->hasPermission('access', 'report/online')) {
                $report[] = array(
                    'name'      => $this->language->get('text_online'),
                    'href'      => $this->url->link('report/online', 'user_token=' . $this->session->data['user_token']),
                    'children'  => array()
                );
            }

            if ($this->user->hasPermission('access', 'report/statistics')) {
                $report[] = array(
                    'name'      => $this->language->get('text_statistics'),
                    'href'      => $this->url->link('report/statistics', 'user_token=' . $this->session->data['user_token']),
                    'children'  => array()
                );
            }

            if ($report) {
                $data['settings'][] = array(
                    'id'        => 'menu-reports',
                    'icon'      => 'fa-chart-bar',
                    'name'      => $this->language->get('text_reports'),
                    'href'      => '',
                    'children'  => $report
                );
            }

            $data['settings'][] = array(
                'id'        => 'menu-logout',
                'icon'      => 'fa-sign-out-alt',
                'name'      => $this->language->get('text_logout'),
                'href'      => $this->url->link('user/logout', 'user_token=' . $this->session->data['user_token']),
                'children'  => array()
            );

            $data['home'] = $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token']);
            $data['profile'] = $this->url->link('user/profile', 'user_token=' . $this->session->data['user_token']);

            # User
            $this->load->model('tool/image');

            $data['username'] = '';
            $data['user_group'] = '';
            $data['image'] = $this->model_tool_image->resize('profile.png', 45, 45);

            $this->load->model('user/user');

            $user_info = $this->model_user_user->getUser($this->user->getId());

            if ($user_info) {
                $data['username'] = $user_info['name'];
                $data['user_group'] = $user_info['user_group'];

                if (is_file(DIR_IMAGE . html_entity_decode($user_info['image'], ENT_QUOTES, 'UTF-8'))) {
                    $data['image'] = $this->model_tool_image->resize(html_entity_decode($user_info['image'], ENT_QUOTES, 'UTF-8'), 45, 45);
                }
            }

            return $this->load->view('common/nav', $data);
        }
    }
}
