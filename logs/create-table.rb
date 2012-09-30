file = File.open("unique") or die "Unable to open file..."

ok = fail = 0
file.each_line { |line|
  puts "INSERT INTO good VALUES ('#{line.strip}');"
}