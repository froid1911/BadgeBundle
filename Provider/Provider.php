<?php

namespace ant\BadgeBundle\Provider;

use ant\BadgeBundle\Composer\ComposerInterface;

use ant\BadgeBundle\Security\ParticipantProviderInterface;

use ant\BadgeBundle\ModelManager\RankManagerInterface;

use ant\BadgeBundle\Event\BadgeEvent;
use ant\BadgeBundle\Model\GroupInterface;
use ant\BadgeBundle\ModelManager\GroupManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use ant\BadgeBundle\ModelManager\BadgeManagerInterface;

/**
 *
 * @author Pablo <pablo@antweb.es>
 */
class Provider implements ProviderInterface
{
	/**
	 * The badge manager
	 *
	 * @var BadgeManagerInterface
	 */
	protected $badgeManager;
	/**
	 * The group manager
	 *
	 * @var GroupManagerInterface
	 */
	protected $groupManager;
	/**
	 * The rank manager
	 *
	 * @var RankManagerInterface
	 */
	protected $rankManager;
	/**
	 * The participant provider instance
	 *
	 * @var ParticipantProviderInterface
	 */
	protected $participantProvider;
	protected $composer;
	
	public function __construct(BadgeManagerInterface $badgeManager, GroupManagerInterface $groupManager, RankManagerInterface $rankManager, ParticipantProviderInterface $participantProvider, ComposerInterface $composer)
	{
		$this->badgeManager = $badgeManager;
		$this->groupManager = $groupManager;
		$this->rankManager = $rankManager;
		$this->participantProvider = $participantProvider;
		$this->composer = $composer;
	}
	
	/**
	 * Gets a badge by its ID
	 *
	 * @return BadgeInterface
	 */
	public function getBadge($badgeId){
		
		$badge = $this->badgeManager->findBadgeById($badgeId);
		return $badge;
	}
	/**
	 * Gets a group by its ID
	 *
	 * @return GroupInterface
	 */
	public function getGroup($groupId){
	
		$group = $this->groupManager->findGroupById($groupId);
		return $group;
	}
	/**
	 * Gets shelf ( allt badges available)
	 *
	 * @return array of BadgeInterface
	 */
	public function getShelf()
	{
		return $this->badgeManager->findAllBadge();
	}
	/**
	 * Gets the group of a class (only one)
	 */
	public function getGroupByClass($class)
	{
		return $this->groupManager->findGroupByClass($class);
	}
	/**
	 * Gets badges of a group
	 * @return array BadgeInterface
	 */
	public function getBadgesByGroup(GroupInterface $group)
	{
		return $this->badgeManager->findBadgesByGroup($group);
	}
	/**
	 * Action for create a first ranking of a user
	 */
	public function RookieUser(BadgeEvent $event)
	{
		$group = $this->getGroupByClass($event->getClass());
		$badges = $this->getBadgesByGroup($group);
		$participant = $this->participantProvider->getAuthenticatedParticipant(); // obtain the user logueado
		
		$totalBadges = sizeof($badges);
		$i = 1;
		$count = 0;
		foreach ($badges as $badge)
		{
			$rank = $this->rankManager->findRankOfBadge($badge, $participant);
			if ($rank == null) {
				$this->composer->createRank($participant, $badge, $count+1);
				return ;
			}
			if ( $rank->getAcquired() == false ) {
				$this->composer->addCountToRank($rank);//Habria que sumar el count
				$this->rankManager->saveRank($rank);
				return;
			}
			if ($totalBadges == $i) {
				$this->composer->addCountToRank($rank);//Habria que sumar el count
				$this->rankManager->saveRank($rank);
				return;
			}
			$count = $rank->getCount();
			$i++;
		}		
		ldd($totalBadges, $i);
		return $badges;
	}
}
