#!/usr/bin/env perl

use strict;
use warnings;
use XML::XPath;
use XML::XPath::XMLParser;

main(@ARGV);
sub main {
    my ($filename) = @_;

    die "Usage: wget http://campusmap.ufl.edu/ufcampus_sde.xml && $0 ufcampus_sde.xml\n"
        unless $filename;
    my $xp = XML::XPath->new(filename => $filename);

    my $buildings = $xp->find('/ufcampus/Z');

    warn "Found " . $buildings->size . " buildings";

    my @lines;
    push @lines, convert_to_tsv($buildings);

    print join "\n", @lines;
}

sub convert_to_tsv {
    my ($nodeset, $type) = @_;

    my @lines;

    foreach my $node ($nodeset->get_nodelist) {
        my $id = $node->getAttribute('id');

        my $raw_name = $node->getAttribute('n');
        my $name = get_building_name($raw_name);
        $name =~ s/,/\\,/g;

        my $lat = $node->getAttribute('lat');
        my $lng = $node->getAttribute('lng');

        push @lines, qq["$name",$lat,$lng];
    }

    return @lines;
}

sub get_building_name {
    my ($name) = @_;

    my @parts = split /\s+-\s+/, $name;

    return $parts[0];
}
