<?php
/**
 * @file
 * @license https://opensource.org/licenses/Apache-2.0 Apache-2.0
 */

namespace Wikimedia\CSS;

use Wikimedia\CSS\Objects\ComponentValueList;
use Wikimedia\CSS\Objects\RuleList;
use Wikimedia\CSS\Objects\SimpleBlock;
use Wikimedia\CSS\Objects\Token;
use Wikimedia\CSS\Objects\TokenList;

/**
 * @covers \Wikimedia\CSS\Util
 */
class UtilTest extends \PHPUnit_Framework_TestCase {

	public function testAllInstanceOf() {
		Util::assertAllInstanceOf( [], Token::class, 'Test' );
		Util::assertAllInstanceOf(
			[ new Token( Token::T_WHITESPACE ), new Token( Token::T_EOF ) ],
			Token::class,
			'Test'
		);

		try {
			Util::assertAllInstanceOf(
				[ new Token( Token::T_WHITESPACE ), null ],
				Token::class,
				'Test'
			);
			$this->fail( 'Expected exception not thrown' );
		} catch ( \InvalidArgumentException $ex ) {
			$this->assertSame(
				'Test may only contain instances of Wikimedia\CSS\Objects\Token (found NULL at index 1)',
				$ex->getMessage()
			);
		}

		try {
			Util::assertAllInstanceOf(
				[ new Token( Token::T_WHITESPACE ), SimpleBlock::newFromDelimiter( Token::T_LEFT_PAREN ) ],
				Token::class,
				'Test'
			);
			$this->fail( 'Expected exception not thrown' );
		} catch ( \InvalidArgumentException $ex ) {
			$this->assertSame(
				'Test may only contain instances of Wikimedia\CSS\Objects\Token '
					. '(found Wikimedia\CSS\Objects\SimpleBlock at index 1)',
				$ex->getMessage()
			);
		}
	}

	public function testAllTokensOfType() {
		Util::assertAllTokensOfType( [], Token::T_EOF, 'Test' );
		Util::assertAllTokensOfType(
			[ new Token( Token::T_EOF ), new Token( Token::T_EOF ) ],
			Token::T_EOF,
			'Test'
		);

		try {
			Util::assertAllTokensOfType(
				[ new Token( Token::T_WHITESPACE ), null ],
				Token::T_WHITESPACE,
				'Test'
			);
			$this->fail( 'Expected exception not thrown' );
		} catch ( \InvalidArgumentException $ex ) {
			$this->assertSame(
				'Test may only contain instances of Wikimedia\CSS\Objects\Token (found NULL at index 1)',
				$ex->getMessage()
			);
		}

		try {
			Util::assertAllTokensOfType(
				[ new Token( Token::T_WHITESPACE ), SimpleBlock::newFromDelimiter( Token::T_LEFT_PAREN ) ],
				Token::T_WHITESPACE,
				'Test'
			);
			$this->fail( 'Expected exception not thrown' );
		} catch ( \InvalidArgumentException $ex ) {
			$this->assertSame(
				'Test may only contain instances of Wikimedia\CSS\Objects\Token '
					. '(found Wikimedia\CSS\Objects\SimpleBlock at index 1)',
				$ex->getMessage()
			);
		}

		try {
			Util::assertAllTokensOfType(
				[ new Token( Token::T_WHITESPACE ), new Token( Token::T_EOF ) ],
				Token::T_WHITESPACE,
				'Test'
			);
			$this->fail( 'Expected exception not thrown' );
		} catch ( \InvalidArgumentException $ex ) {
			$this->assertSame(
				'Test may only contain "whitespace" tokens (found "EOF" at index 1)',
				$ex->getMessage()
			);
		}
	}

	public function testStringify() {
		// Each object's toTokenArray() is already tested and each token type's
		// as well. Here we just need to test that combining works right.

		$tokenList = new TokenList( [
			new Token( Token::T_LEFT_BRACE ),
			new Token( Token::T_WHITESPACE ),
			new Token( Token::T_IDENT, 'Hello' ),
			new Token( Token::T_WHITESPACE, [ 'significant' => false ] ),
			new Token( Token::T_IDENT, 'wor' ),
			new Token( Token::T_IDENT, 'ld' ),
			new Token( Token::T_DELIM, '!' ),
			new Token( Token::T_WHITESPACE, [ 'significant' => false ] ),
			new Token( Token::T_DELIM, '!' ),
			new Token( Token::T_WHITESPACE, [ 'significant' => false ] ),
			new Token( Token::T_RIGHT_BRACE ),
		] );

		$this->assertSame( '{ Hello wor/**/ld! ! }', Util::stringify( $tokenList ) );
		$this->assertSame( '{ Hello wor/**/ld!!}', Util::stringify( $tokenList, [ 'minify' => true ] ) );

		$this->assertSame( '', Util::stringify( new TokenList() ) );
	}
}