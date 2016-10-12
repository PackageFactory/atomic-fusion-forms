<?php
namespace PackageFactory\AtomicFusion\Forms\Fusion;

/**
 * This file is part of the PackageFactory.AtomicFusion.Forms package
 *
 * (c) 2016 Wilhelm Behncke <wilhelm.behncke@googlemail.com>
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\TypoScript\TypoScriptObjects\ArrayImplementation;

class PagesImplementation extends ArrayImplementation
{
	/**
	 * @var array
	 */
	protected $pages;

	public function evaluate()
	{
		$this->pages = $this->sortNestedTypoScriptKeys();
		return $this;
	}

	public function getInitialPage()
	{
		if (!isset($this->pages[0])) {
			throw new \Exception(
				'There are no pages defined yet. Make sure to have a `pages` key in your Form component',
				1475674275
			);
		}
		return $this->pages[0];
	}

	public function getNextPage($currentPage)
	{
		if ($this->pages) {
			$pages = $this->pages;
			if (($currentPageIndex = array_search($currentPage, $pages)) !== false) {
				return isset($pages[$currentPageIndex + 1]) ? $pages[$currentPageIndex + 1] : null;
			}

			throw new \Exception(sprintf('Error while fetching page: Page `%s` does not exist.', $currentPage), 1475479580);
		}
	}

	public function renderPage($pageIdentifier)
	{
		if (!$this->tsRuntime->canRender(sprintf('%s/%s', $this->path, $pageIdentifier))) {
			throw new \Exception(sprintf('Error while rendering page: Page `%s` does not exist.', $pageIdentifier), 1475479372);
		}

		return $this->tsRuntime->render(sprintf('%s/%s', $this->path, $pageIdentifier));
	}
}
