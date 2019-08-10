workflow "Main" {
  on = "push"
  resolves = ["PHPStan"]
}

action "PHPStan" {
  uses = "docker://oskarstark/phpstan-ga"
  args = "analyse src/"
}