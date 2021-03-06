<?php

namespace Sandertv\BlockSniper\commands\cloning;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use Sandertv\BlockSniper\commands\BaseCommand;
use pocketmine\utils\TextFormat as TF;
use Sandertv\BlockSniper\Loader;
use Sandertv\BlockSniper\cloning\Copy;
use Sandertv\BlockSniper\cloning\Template;

class CloneCommand extends BaseCommand {
	
	public function __construct(Loader $owner) {
		parent::__construct($owner, "clone", "Clone the area you're watching", "<type> <radiusXheight>", []);
		$this->setPermission("blocksniper.command.clone");
	}
	
	/**
	 * @param CommandSender $sender
	 * @param type          $commandLabel
	 * @param array         $args
	 *
	 * @return boolean
	 */
	public function execute(CommandSender $sender, $commandLabel, array $args) {
		if(!$this->testPermission($sender)) {
			$this->sendNoPermission($sender);
			return true;
		}
		
		if(!$sender instanceof Player) {
			$this->sendConsoleError($sender);
			return true;
		}
		
		if(count($args) < 2 || count($args) > 2) {
			$sender->sendMessage(TF::RED . "[Usage] /clone <type> <radiusXheight>");
			return true;
		}
		
		$sizes = explode("x", strtolower($args[1]));
		
		if((int) $sizes[0] > $this->getSettings()->get("Maximum-Radius") || (int) $sizes[1] > $this->getSettings()->get("Maximum-Radius")) {
			$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.radius-too-big"));
			return true;
		}
		
		$center = $sender->getTargetBlock(100);
		if(!$center) {
			$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.no-target-found"));
			return true;
		}
		
		switch(strtolower($args[0])) {
			case "copy":
				$clone = new Copy($this->getPlugin(), $sender->getLevel(), $center, $sizes[0], $sizes[1]);
				break;
				
			case "template":
				$clone = new Template($this); // TODO
				break;
				
			default:
				$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.shape-not-found"));
				return true;
		}
	}
}
