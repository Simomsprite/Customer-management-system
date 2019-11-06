<?php

class VisitorPlanViewModel extends ViewModel
{
    protected $viewFields = array(
        'visitor_plan' => array('*'),
        'event' => array(
            '_on' => 'visitor_plan.event_id=event.event_id',
            '_type' => 'LEFT',
            'owner_role_id',
            'module',
            'module_id'
        )
    );
}