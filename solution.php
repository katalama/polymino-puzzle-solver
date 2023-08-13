<?php

$frame8x8 = [
	[1,1,1,1,1,1,1,1,1,1],
	[1,0,0,0,0,0,0,0,0,1],
	[1,0,0,0,0,0,0,0,0,1],
	[1,0,0,0,0,0,0,0,0,1],
	[1,0,0,0,0,0,0,0,0,1],
	[1,0,0,0,0,0,0,0,0,1],
	[1,0,0,0,0,0,0,0,0,1],
	[1,0,0,0,0,0,0,0,0,1],
	[1,0,0,0,0,0,0,0,0,1],
	[1,1,1,1,1,1,1,1,1,1],
];

$pieces = [
	unserializePiece('010-111-010'), // 5 
	unserializePiece('111-010-010'), // 5
	unserializePiece('011-111-111'), // 8
	unserializePiece('101-111'), // 5
	unserializePiece('1111-0011'), // 6
	unserializePiece('111-010-010-010'), // 6 
	unserializePiece('100-110-011'), // 5
	unserializePiece('111-011'), // 5
	unserializePiece('1001-1111'), // 6
	unserializePiece('0001-0001-0001-1111'), // 7
	unserializePiece('11-10-10-10-10'), // 6
];

// horizontal
function flipPiece(array $piece): array
{
	return array_reverse($piece);
}

function rot90Piece(array $piece): array
{
	$rotPiece = [];
	
	foreach ($piece as $i=>$p) {
		foreach ($p as $j=>$v) {
			$rotPiece[$j][$i] = $piece[$i][$j];
		}
	}
	
	return flipPiece($rotPiece);
}

function serializePiece(array $piece): string
{
	$lines = [];
	
	foreach ($piece as $lineCfg) {
		$lines[] = implode('', $lineCfg);
	}
	
	return implode('-', $lines);
}

function unserializePiece(string $key): array
{
	$piece = [];
	
	foreach (explode('-', $key) as $line) {
		$piece[] = array_map('intval', str_split($line));
	}
	
	return $piece;
}

function getVariants(array $piece): array
{
	$variants = [];
	
	$piece = rot90Piece($piece);
	$variants[serializePiece($piece)] = $piece;

	$piece = rot90Piece($piece);
	$variants[serializePiece($piece)] = $piece;

	$piece = rot90Piece($piece);
	$variants[serializePiece($piece)] = $piece;

	$piece = rot90Piece($piece);
	$variants[serializePiece($piece)] = $piece;

	$piece = flipPiece($piece);
	
	$piece = rot90Piece($piece);
	$variants[serializePiece($piece)] = $piece;

	$piece = rot90Piece($piece);
	$variants[serializePiece($piece)] = $piece;

	$piece = rot90Piece($piece);
	$variants[serializePiece($piece)] = $piece;

	$piece = rot90Piece($piece);
	$variants[serializePiece($piece)] = $piece;
	
	return $variants;
}

function combinePieces(array $locatedPieces)
{
	$result = [];
	foreach ($locatedPieces as $pieceKey => $position) {
		$piece = unserializePiece($pieceKey);
		[$pi, $pj] = $position;
		for ($i=0; $i<count($piece); $i++) {
			for ($j=0; $j<count($piece[0]); $j++) {
				$value = $result[$i+$pi][$j+$pj] ?? 0;
				$result[$i+$pi][$j+$pj] = $value + $piece[$i][$j];
			}
		}
	}
	
	return $result;
}

function printPiece($piece)
{
	$key = serializePiece($piece);
	foreach (explode('-', $key) as $line) {
		echo $line . PHP_EOL;
	}
	echo PHP_EOL;
}

function printPieceHtml($piece)
{
	$s = '<style>td {width:10px; height:10px;border:0px}</style><table>';
	$colors = [
		'A' => 'FFF8DC',
		'B' => 'BC8F8F',
		'C' => 'F4A460',
		'D' => '8B4513',
		'E' => 'A0522D',
		'F' => '800000',
		'G' => 'DDA0DD',
		'H' => 'FF00FF',
		'I' => '8A2BE2',
		'J' => '8B008B',
		'K' => '4B0082',
		'L' => 'FFFF00',
		'M' => 'F0E68C',
		'N' => 'FF7F50',
		'O' => 'FF69B4',
		'P' => 'ADFF2F',
		'Q' => '32CD32',
		'R' => '2E8B57',
		'S' => '00FFFF',
		'T' => '00CED1',
		'U' => '4682B4',
		'V' => '00008B',
		'W' => '808080',
		'X' => '000000',
		'Y' => 'F5F5DC',
		'Z' => 'FAEBD7',
	];
	$key = serializePiece($piece);
	foreach (explode('-', $key) as $line) {
		$s .= '<tr>';
		foreach (str_split($line) as $c) {
			$s .= '<td style="background-color:#' . ($colors[$c] ?? 'ccc') . '"></td>';
		}
		$s .= '</tr>';
	}
	
	$s .= '</table><br/>';
	echo $s;
	return $s;
}

function getFirstEmptyPosition($piece): array
{
	for ($i=0; $i<count($piece); $i++) {
		for ($j=0; $j<count($piece[0]); $j++) {
			if ($piece[$i][$j] === 0) {
				return [$i, $j];
			}
		}
	}

	return [];
}

function getFirstNonEmptyPosition($piece): array
{
	for ($i=0; $i<count($piece); $i++) {
		for ($j=0; $j<count($piece[0]); $j++) {
			if ($piece[$i][$j] === 1) {
				return [$i, $j];
			}
		}
	}

	return [];
}

function canBeCombined($locatedPieces): bool
{
	$piece = combinePieces($locatedPieces);
	
	for ($i=0; $i<count($piece); $i++) {
		for ($j=0; $j<count($piece[0]); $j++) {
			if ($piece[$i][$j] === 2) {
				return false;
			}
		}
	}

	return true;
}

function printSet(array $locatedPieces)
{
	$combinedPieces = combinePieces($locatedPieces);
	$n = 65;
	foreach ($locatedPieces as $pieceKey => $position) {
		$piece = unserializePiece($pieceKey);
		[$pi, $pj] = $position;
		$chr = chr($n++);
		for ($i=0; $i<count($piece); $i++) {
			for ($j=0; $j<count($piece[0]); $j++) {
				if ($piece[$i][$j]) {
					$combinedPieces[$i+$pi][$j+$pj] = $chr;
				}
			}
		}
	}
	
	printPieceHtml($combinedPieces);
}

function solve(array $currentSet, array $pieces)
{
	$results = [];
	
	if (empty($pieces)) {
		// echo '+';
		printSet($currentSet);
		return [$currentSet];
	}
	
	$combinedSet = combinePieces($currentSet);
	$emptyPosition = getFirstEmptyPosition($combinedSet);
	
	if (empty($emptyPosition)) {
		return [];
	}
	
	foreach ($pieces as $k=>$piece) {
		foreach (getVariants($piece) as $pieceVariant) {
			// should be always not null
			$nonEmptyPosition = getFirstNonEmptyPosition($pieceVariant);
			
			if (false === $nonEmptyPosition) {
				continue;
			}
			
			[$i, $j] = $emptyPosition;
			[$pi, $pj] = $nonEmptyPosition;
			
			if (canBeCombined([
					serializePiece($combinedSet) => [0, 0], 
					serializePiece($pieceVariant) => [$i - $pi, $j - $pj],
				])
			) {
				$piecesCopy = $pieces;
				$setCopy = $currentSet;
				
				$setCopy[serializePiece($pieceVariant)] = [$i - $pi, $j - $pj];
				unset($piecesCopy[$k]);
				
				$subResult = solve($setCopy, $piecesCopy);
				
				$results = array_merge($results, $subResult);
			} else {
				// echo '.';
			}
		}
	}
	
	return $results;
}

$time = microtime(true);
// shuffle($pieces);
$solutions = solve(
	[serializePiece($frame8x8) => [0, 0]],
	$pieces
);

var_dump($solutions, microtime(true) - $time, memory_get_peak_usage());
// foreach ($solutions as $solution) {
// 	printSet($solution);
// }
