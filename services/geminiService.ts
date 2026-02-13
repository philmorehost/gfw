
import { GoogleGenAI, Type, GenerateContentParameters } from "@google/genai";
import { Category, Post, AIModel } from "../types";

function getSoccerImage(category: Category): string {
  const images: Record<string, string[]> = {
    [Category.EPL]: ["https://images.unsplash.com/photo-1574629810360-7efbbe195018"],
    [Category.TRANSFERS]: ["https://images.unsplash.com/photo-1551958219-acbc608c6377"],
    [Category.UCL]: ["https://images.unsplash.com/photo-1556056504-5c7696c4c28d"],
    [Category.LALIGA]: ["https://images.unsplash.com/photo-1522778119026-d647f0596c20"]
  };
  const pool = images[category] || images[Category.EPL];
  const randomImg = pool[Math.floor(Math.random() * pool.length)];
  return `${randomImg}?auto=format&fit=crop&q=80&w=800`;
}

/**
 * Robust wrapper for Gemini calls.
 * Specifically handles the "Rpc failed" or "Requested entity was not found" (404)
 * by retrying without tools, which is a common cause of such errors in restricted environments.
 */
async function callGemini(params: GenerateContentParameters): Promise<any> {
  const ai = new GoogleGenAI({ apiKey: process.env.API_KEY });
  
  try {
    return await ai.models.generateContent(params);
  } catch (error: any) {
    const errorMsg = error?.message || "";
    const isInternalError = error?.code === 500 || errorMsg.includes("Rpc failed") || errorMsg.includes("xhr error");
    const isNotFoundError = error?.code === 404 || errorMsg.includes("Requested entity was not found");
    const isToolError = errorMsg.includes("grounding") || errorMsg.includes("tool") || isNotFoundError;

    // If we have an error and we were using tools, retry without tools
    if ((isInternalError || isToolError) && params.config?.tools) {
      console.warn("Gemini Feature/Tool Error detected (Code: " + (error?.code || 'UNK') + "). Falling back to base model without tools...", error);
      const fallbackConfig = { ...params.config };
      delete fallbackConfig.tools; // Strip Google Search tool
      
      // Attempt the call again without tools to ensure the user gets a response
      return await ai.models.generateContent({
        ...params,
        config: fallbackConfig
      });
    }
    throw error;
  }
}

/**
 * Fetches real-time sports data with reliable fallbacks.
 */
export async function fetchSportsData(
  type: 'LIVESCORE' | 'STATS' | 'LINEUPS' | 'RESULTS' | 'SCHEDULE' | 'ODDS' | 'PROFILES',
  competition: string,
  model: AIModel = 'gemini-3-flash-preview'
): Promise<string> {
  const prompt = `Provide the latest ${type.toLowerCase()} for ${competition}. 
  Context: This is for a professional football news site. 
  Focus on real-time accuracy and tactical depth. 
  Format the output nicely in Markdown with bold headers.`;

  try {
    const response = await callGemini({
      model,
      contents: [{ parts: [{ text: prompt }] }],
      config: {
        tools: [{ googleSearch: {} }],
        temperature: 0.7,
      }
    });

    const grounding = response.candidates?.[0]?.groundingMetadata?.groundingChunks || [];
    const citations = grounding.map((chunk: any) => chunk.web?.uri).filter(Boolean);
    
    let resultText = response.text || "No data returned from AI.";
    if (citations.length > 0) {
      const uniqueCitations = Array.from(new Set(citations)) as string[];
      resultText += "\n\n**VERIFIED SOURCES:**\n" + uniqueCitations.map(url => `- [${new URL(url).hostname}](${url})`).join('\n');
    }
    
    return resultText;
  } catch (error: any) {
    console.error(`Final Gemini Failure (${type}):`, error);
    return `### DATA STREAM INTERRUPTED\nOur AI Scout is currently recalibrating. This usually happens when live grounding tools are unavailable for the requested model. \n\n**Try refreshing or selecting a different data type.**`;
  }
}

export async function fetchAndRefineNews(topic: string, category: Category, model: AIModel = 'gemini-3-flash-preview'): Promise<Post[]> {
  try {
    const searchResponse = await callGemini({
      model,
      contents: [{ parts: [{ text: `Generate 3 distinct, high-impact news headlines and summaries about ${topic} in the ${category} category.` }] }],
      config: { tools: [{ googleSearch: {} }] }
    });

    const searchResults = searchResponse.text;

    const refineResponse = await callGemini({
      model,
      contents: [{ parts: [{ text: `Based on this data: "${searchResults}", refine it into 3 polished news articles for "The Global Football Watch". Return as a JSON array.` }] }],
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
      isTopStory: Math.random() > 0.8
    }));
  } catch (error) {
    console.error("Gemini News Refinement Failure:", error);
    return [];
  }
}

export async function generateNewsArticle(title: string, category: Category, model: AIModel = 'gemini-3-flash-preview'): Promise<Partial<Post>> {
  try {
    const response = await callGemini({
      model,
      contents: [{ parts: [{ text: `Draft a professional football news article for: "${title}" in category: "${category}". Return JSON.` }] }],
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
    return {};
  }
}

export async function generatePostImage(prompt: string): Promise<string | null> {
  const ai = new GoogleGenAI({ apiKey: process.env.API_KEY });
  try {
    const response = await ai.models.generateContent({
      model: 'gemini-3-pro-image-preview',
      contents: { parts: [{ text: `Hyper-realistic, cinematic stadium photography: ${prompt}` }] },
      config: { imageConfig: { aspectRatio: "16:9", imageSize: "1K" } },
    });
    const part = response.candidates?.[0]?.content?.parts.find(p => p.inlineData);
    return part ? `data:image/png;base64,${part.inlineData.data}` : null;
  } catch (error) {
    return null;
  }
}

export async function getAIFootballInsight(query: string, model: AIModel = 'gemini-3-flash-preview'): Promise<string> {
  try {
    const response = await callGemini({
      model,
      contents: [{ parts: [{ text: query }] }],
    });
    return response.text || "Tactical data currently unavailable.";
  } catch (error) {
    return "Insight system offline.";
  }
}
