<?php
namespace NIMIUS\NewsBlog\Hook;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
use GeorgRinger\News\Domain\Repository\NewsRepository;
use NIMIUS\NewsBlog\Utility\ConfigurationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * News repository hook.
 *
 * Modifies the news query based on additional settings.
 */
class NewsRepositoryHook
{
    /**
     * @var array
     */
    protected $settings;

    /**
     * Query modify method.
     *
     * Appends constraints to the news query based on available custom settings.
     *
     * @param array &$params An array with all required objects in it
     * @param NewsRepository $newsRepository The repository instance
     * @return void
     */
    public function modify(array &$params, NewsRepository $newsRepository)
    {
        $this->getSettings();

        $authors = null;

        // Add authors constraint
        if (!empty($this->settings['authors'])) {
            $authors = GeneralUtility::intExplode(',', $this->settings['authors'], true);
        } else {
            $gp = GeneralUtility::_GP('tx_news_pi1');
            if ($gp['overwriteDemand'] && $gp['overwriteDemand']['authorId']) {
                $authors = GeneralUtility::intExplode(',', $gp['overwriteDemand']['authorId'], true);
            }
        }
        if ($authors) {
            $params['constraints'][] = $params['query']->in('author_id', $authors);
        }
    }

    /**
     * Prepares TypoScript settings.
     *
     * @return void
     */
    protected function getSettings()
    {
        $this->settings = ConfigurationUtility::getPluginTyposcriptConfiguration('news');
    }
}
