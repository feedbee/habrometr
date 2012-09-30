=begin
input: ruby 01-concatinate-logs.rb dir filename
=end

def get_postfix (el)
  match = el.match(/\.(\d{1,2})$/)
  if match then match[1].to_i else 0 end
end

if $*.length != 2
  p "Usage: ruby process.rb dir file"
  exit
end

dirname = $*[0]
if ! File.directory? dirname
  puts "`#{dirname}` is not a directory"
  exit
end

filename = $*[1]

entries = Dir.entries(".").select {|el| File.file? el and el.include? "access.log"}
entries.sort! {|a, b|  get_postfix(a) <=> get_postfix(b) }

`echo -n > #{filename}`
entries.each {|file|
  `cat #{file} >> #{filename}`
  puts file
}
puts "Done"