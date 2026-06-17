/**
 * voice_utils.js — ImmobiVoice AI
 * Multilingual voice transcription normalizer.
 *
 * Normalizes spoken "@" symbol variants that are UNAMBIGUOUS
 * (language-specific words that can only mean "@") into the actual @.
 * Ambiguous words like "punto", "dot", "at", "et" are intentionally
 * left for GPT to resolve using sentence context.
 *
 * Languages: it-IT · en-US · fr-FR · es-ES · pt-BR · de-DE
 */
const VoiceUtils = (() => {
  // Only word-patterns that are UNAMBIGUOUSLY the @ symbol.
  const AT_PATTERNS = [
    /\bchiocciola\b/gi,    // Italian  — "la chiocciola" = snail = @
    /\barroba\b/gi,         // Spanish / Portuguese
    /\barobase\b/gi,        // French
    /\bklammeraffe\b/gi,    // German informal ("cling-monkey")
    /\bat\s+sign\b/gi,      // English verbose: "at sign"
  ];

  /**
   * Replace unambiguous spoken "@" variants with the @ character.
   * Also strips surrounding spaces introduced by speech segmentation
   * (e.g. "mario chiocciola gmail" → "mario@gmail").
   * @param {string} text  Raw Speech API transcript
   * @returns {string}     Normalised text
   */
  function normalizeAt(text) {
    let out = text;
    for (const p of AT_PATTERNS) {
      out = out.replace(p, '@');
    }
    // Remove spaces that speech inserted around @
    out = out.replace(/\s*@\s*/g, '@');
    return out;
  }

  return { normalizeAt };
})();
