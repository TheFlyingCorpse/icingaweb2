<?php
// {{{ICINGA_LICENSE_HEADER}}}
// {{{ICINGA_LICENSE_HEADER}}}

namespace Icinga\Module\Monitoring\DataView;

/**
 * View for programstatus query
 */
class Programstatus extends DataView
{
    /**
     * Retrieve columns provided by this view
     *
     * @return array
     */
    public function getColumns()
    {
        return array(
            'id',
            'status_update_time',
            'program_start_time',
            'program_end_time',
            'is_currently_running',
            'process_id',
            'daemon_mode',
            'last_command_check',
            'last_log_rotation',
            'notifications_enabled',
            'disable_notif_expire_time',
            'active_service_checks_enabled',
            'passive_service_checks_enabled',
            'active_host_checks_enabled',
            'passive_host_checks_enabled',
            'event_handlers_enabled',
            'flap_detection_enabled',
            'failure_prediction_enabled',
            'process_performance_data',
            'obsess_over_hosts',
            'obsess_over_services',
            'modified_host_attributes',
            'modified_service_attributes',
            'global_host_event_handler',
            'global_service_event_handler',
        );
    }

    /**
     * Retrieve default sorting rules for particular columns. These involve sort order and potential additional to sort
     *
     * @return array
     */
    public function getSortRules()
    {
        return array(
            'id' => array(
                'order' => self::SORT_DESC
            )
        );
    }
}
