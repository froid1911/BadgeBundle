<?php

namespace ant\BadgeBundle\Composer;

use ant\BadgeBundle\Model\RankInterface;

use ant\BadgeBundle\Model\ParticipantInterface;

use ant\BadgeBundle\Model\BadgeInterface;

/**
 * Factory for badge builders
 *
 * @author Pablo <pablo@antweb.es>
 */
interface ComposerInterface
{
    /**
     * Starts composing a badge
     *
     * @return NewBadgeBuilder
     */
    public function newBadge();
    /**
     * Starts composing a rank
     *
     * @return NewRankBuilder
     */
    public function newRank();
    
    public function createRank(ParticipantInterface $participant, BadgeInterface $badge);

    public function addCountToRank(RankInterface $rank);
}
