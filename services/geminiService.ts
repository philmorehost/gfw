
import { GoogleGenAI, Type } from "@google/genai";
import { Category, Post } from "../types";

/**
 * Helper to get a realistic soccer image URL based on keywords
 */
function getSoccerImage(category: Category): string {
  const images: Record<string, string[]> = {
    [Category.EPL]: [
      "https://images.unsplash.com/photo-1574629810360-7efbbe195018",
      "https://images.unsplash.com/photo-1522778119026-d647f0596c20",
      "https://images.unsplash.com/photo-1517466787929-bc90951d0974"
    ],
    [Category.TRANSFERS]: [
      "https://images.unsplash.com/photo-1551958219-acbc608c6377",
      "https://images.unsplash.com/photo-1508098682722-e99c43a406b2",
      "https://images.unsplash.com/photo-1518091043644-c1d4457512c6"
    ],
    [Category.UCL]: [
      "https://images.unsplash.com/photo-1556056504-5c7696c4c28d",
      "https://images.unsplash.com/photo-1543351611-58f69d7c1781",
      "https://images.unsplash.com/photo-1624880351055-97c5270979bb"
    ]
  };

  const pool = images[category] || images[Category.EPL];
  const randomImg = pool[Math.floor(Math.random() * pool.length)];
  return `${randomImg}?auto=format&fit=crop&q=80&w=800`;
}

/**
 * Fetches real news via Google Search and refines it into a unique article.
 * Uses a two-step process to follow Search Grounding guidelines.
 */
export async function fetchAndRefineNews(topic: string, category: Category): Promise<Post[]> {
  const ai = new GoogleGenAI({ apiKey: process.env.API_KEY });
  try {
    // Step 1: Use Google Search to find latest information
    const searchResponse = await ai.models.generateContent({
      model: "gemini-3-flash-preview",
      contents: `Find the 3 latest and most important news stories about ${topic} in the ${category} category for a football news site. Focus on real-time events.`,
      config: {
        tools: [{ googleSearch: {} }],
      }
    });

    const searchResults = searchResponse.text;

    // Step 2: Use the search results to generate structured articles in JSON format
    const refineResponse = await ai.models.generateContent({
      model: "gemini-3-flash-preview",
      contents: `Based on these news results:
      "${searchResults}"
      
      Refine them into 3 unique, high-quality articles for "The Global Football Watch".
      Category: ${category}.
      Return as a JSON array of objects with "title", "excerpt", "content", and "author". 
      Rewrite them as professional, original sports reporting. Do not include external links.`,
      config: {
        responseMimeType: "application/json",
        responseSchema: {
          type: Type.ARRAY,
          items: {
            type: Type.OBJECT,
            properties: {
              title: { type: Type.STRING },
              excerpt: { type: Type.STRING },
              content: { type: Type.STRING },
              author: { type: Type.STRING }
            },
            required: ["title", "excerpt", "content", "author"]
          }
        }
      }
    });

    const newsData = JSON.parse(refineResponse.text || '[]');
    return newsData.map((item: any) => ({
      ...item,
      id: Math.random().toString(36).substr(2, 9),
      category,
      date: new Date().toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }),
      image: getSoccerImage(category),
      isTopStory: Math.random() > 0.7
    }));
  } catch (error) {
    console.error("Gemini News Refinement Error:", error);
    return [];
  }
}

/**
 * Generates a full news article draft based on a title and category.
 */
export async function generateNewsArticle(title: string, category: Category): Promise<Partial<Post>> {
  const ai = new GoogleGenAI({ apiKey: process.env.API_KEY });
  try {
    const response = await ai.models.generateContent({
      model: "gemini-3-flash-preview",
      contents: `You are a professional football journalist for "The Global Football Watch". Draft a detailed, expert news article for the headline: "${title}" in the category: "${category}". 
      Return the response as a JSON object with "excerpt", "content", and "author" fields.`,
      config: {
        responseMimeType: "application/json",
        responseSchema: {
          type: Type.OBJECT,
          properties: {
            excerpt: { type: Type.STRING },
            content: { type: Type.STRING },
            author: { type: Type.STRING }
          },
          required: ["excerpt", "content", "author"]
        }
      }
    });

    return JSON.parse(response.text || '{}');
  } catch (error) {
    console.error("Gemini Article Generation Error:", error);
    return {};
  }
}

/**
 * Generates an image for a football post.
 * Uses gemini-3-pro-image-preview for high-quality sports photography.
 */
export async function generatePostImage(prompt: string): Promise<string | null> {
  const ai = new GoogleGenAI({ apiKey: process.env.API_KEY });
  try {
    const response = await ai.models.generateContent({
      model: 'gemini-3-pro-image-preview',
      contents: { parts: [{ text: `Professional 4k cinematic sports photography, hyper-realistic, action shot of: ${prompt}. Intense lighting, sharp focus.` }] },
      config: { 
        imageConfig: { 
          aspectRatio: "16:9", 
          imageSize: "1K" 
        } 
      },
    });
    
    const part = response.candidates?.[0]?.content?.parts.find(p => p.inlineData);
    return part ? `data:image/png;base64,${part.inlineData.data}` : null;
  } catch (error) {
    console.error("Gemini Image Generation Error:", error);
    return null;
  }
}

/**
 * Provides an elite tactical analysis of football news.
 */
export async function getAIFootballInsight(query: string): Promise<string> {
  const ai = new GoogleGenAI({ apiKey: process.env.API_KEY });
  try {
    const response = await ai.models.generateContent({
      model: "gemini-3-flash-preview",
      contents: query,
      config: { 
        thinkingConfig: { thinkingBudget: 0 } 
      }
    });
    return response.text || "No insights available.";
  } catch (error) {
    console.error("Gemini Insight Error:", error);
    return "Insights are currently unavailable.";
  }
}
