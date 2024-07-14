<?php

/**
 * Class PhenyxMaintenance
 *
 * This class implements tasks for maintaining hte ephenyx digital installation, to be
 * run on a regular schedule. It gets called by an asynchronous Ajax request
 * in DashboardController.
 *
 * @since 1.0.8
 */
class PhenyxMaintenance {

    /**
     * Run tasks as needed. Should take care of running tasks not more often
     * than needed and that one run takes not longer than a few seconds.
     *
     * This method gets triggered by the 'getNotifications' Ajax request, so
     * every two minutes while somebody has back office open.
     *
     * @since 1.0.8
     */
    public static function run() {

        $now = time();
        $lastRun = Configuration::get('PHENYX_MAINTENANCE_LAST_RUN');

        if ($now - $lastRun > 86400) {
            // Run daily tasks.
            PhenyxTools::cleanBackTabs();
            PhenyxTools::cleanMetas();
            PhenyxTools::cleanPlugins();
            PhenyxTools::cleanHook();

            Configuration::updateGlobalValue('PHENYX_MAINTENANCE_LAST_RUN', $now);
        }

    }

    
}
