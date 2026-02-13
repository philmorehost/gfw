
export enum Category {
  EPL = 'Premier League',
  UCL = 'Champions League',
  UEL = 'Europa League',
  LALIGA = 'La Liga',
  SERIEA = 'Serie A',
  BUNDESLIGA = 'Bundesliga',
  TRANSFERS = 'Transfers',
  CHELSEA = 'Chelsea',
  ARSENAL = 'Arsenal',
  LIVERPOOL = 'Liverpool',
  MANCITY = 'Man City',
  FIXTURES = 'Fixtures',
  TABLE = 'Table',
  SCORERS = 'Top Scorers'
}

export type AIModel = 'gemini-3-flash-preview' | 'gemini-3-pro-preview' | 'gemini-2.5-flash-lite-latest';

export interface SEOMetadata {
  metaTitle: string;
  metaDescription: string;
  keywords: string;
}

export interface Post {
  id: string;
  title: string;
  excerpt: string;
  content: string;
  category: Category;
  author: string;
  date: string;
  image: string;
  isTopStory?: boolean;
  seo?: SEOMetadata;
  tags?: string[];
}

export interface Match {
  id: string;
  homeTeam: string;
  homeLogo?: string;
  awayTeam: string;
  awayLogo?: string;
  homeScore?: number;
  awayScore?: number;
  time: string;
  status: 'SCHEDULED' | 'LIVE' | 'IN_PLAY' | 'PAUSED' | 'FINISHED' | 'POSTPONED' | 'CANCELLED';
  league: string;
}

export interface Standing {
  position: number;
  team: string;
  played: number;
  won: number;
  draw: number;
  lost: number;
  points: number;
  goalsFor: number;
  goalsAgainst: number;
}

export interface Transfer {
  player: string;
  from: string;
  to: string;
  fee: string;
  status: 'RUMOUR' | 'OFFICIAL' | 'NEGOTIATION';
}

export interface SiteSettings {
  name: string;
  tagline: string;
  logo: string;
  adminEmail: string;
  smtpSender: string;
  smtpConfig?: {
    host: string;
    port: string;
    user: string;
    pass: string;
  };
  whatsappNumber: string;
  selectedModel: AIModel;
  socials: {
    facebook: string;
    twitter: string;
    instagram: string;
    youtube: string;
  };
}

export type CommentStatus = 'pending' | 'approved' | 'rejected' | 'spam';

export interface Comment {
  id: string;
  postId: string;
  postTitle?: string;
  author: string;
  text: string;
  date: string;
  status: CommentStatus;
}

export interface Subscriber {
  email: string;
  dateJoined: string;
}
