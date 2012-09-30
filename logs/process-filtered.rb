file = File.open("full_filtered") or die "Unable to open file..."

ok = fail = 0
file.each_line { |line|
  match_data = line.match(/\/habrometr_\d{1,3}x\d{1,3}_([^\s]*)\.png(\?rand=\d+)?/)
  match_data = line.match(/\/habrometr_\d{1,3}x\d{1,3}\.php\?user=([^\s]*)/) if match_data.nil?
  if !match_data.nil?
    puts match_data[1].downcase + "\n"
    ok += 1
  else
    fail += 1
  end
}

$stderr << "Success: #{ok.to_s}\n" << "Fail: #{fail.to_s}\n"