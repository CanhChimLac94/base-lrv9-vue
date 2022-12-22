{ pkgs }: {
	deps = [
    pkgs.sudo
		pkgs.php80
    pkgs.php80Packages.composer
    # pkgs.php82
    # pkgs.php82Packages.composer
    # pkgs.docker
    # pkgs.docker-compose
	];
}